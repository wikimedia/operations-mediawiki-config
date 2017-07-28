"""
Updates wiki versions json files and symlink pointers
"""

import json
import os
import subprocess

import scap.cli as cli
import scap.utils as utils


@cli.command('update-wikiversions')
class UpdateWikiversions(cli.Application):
    """ Scap subcommand for updating wikiversions.json to a new version"""

    @cli.argument('dblist',
                  help='The dblist file to use as input for migrating.')
    @cli.argument('branch', help='The name of the branch to migrate to.')
    def main(self, *extra_args):
        self.update_wikiversions_json()
        self.update_branch_pointer()

    def update_wikiversions_json(self):
        """
        Does the first step of migrating: actually change all the requested
        dblist entries to the new version!
        """
        json_path = utils.get_realm_specific_filename(
            'wikiversions.json', self.config['wmf_realm'],
            self.config['datacenter'])

        db_list_name = os.path.basename(
            os.path.splitext(self.arguments.dblist)[0])

        script = os.path.join(
            self.config['stage_dir'], 'multiversion', 'bin', 'expanddblist')
        dblist = subprocess.check_output([script, db_list_name]).splitlines()

        new_dir = 'php-%s' % self.arguments.branch

        if not os.path.isdir(os.path.join(self.config['stage_dir'], new_dir)):
            raise ValueError('Invalid version specifier: %s' % new_dir)

        if os.path.exists(json_path):
            with open(json_path) as json_in:
                version_rows = json.load(json_in)
        else:
            if db_list_name is not 'all':
                raise RuntimeError(
                    'No %s file and not invoked with "all."' % json_path +
                    'Cowardly refusing to act.'
                )
            self.get_logger().info('%s not found -- rebuilding from scratch!' %
                                   json_path)
            version_rows = {}

        inserted = 0
        migrated = 0

        for dbname in dblist:
            if dbname in version_rows:
                inserted += 1
            else:
                migrated += 1
            version_rows[dbname] = new_dir

        with open(json_path, 'w') as json_out:
            json.dump(version_rows, json_out, ensure_ascii=False, indent=4,
                      separators=(',', ':'), sort_keys=True)

        self.get_logger().info('Updated %s: %s inserted, %s migrated.' %
                               (json_path, inserted, migrated))

    def update_branch_pointer(self):
        """
        This being the second step: if appropriate, swap the php symlink over
        to the new version as well
        """
        cur_version = self.active_wikiversions().popitem()[0]

        utils.move_symlink(
            os.path.join(self.config['stage_dir'], 'php'),
            os.path.join(self.config['stage_dir'],
                         'php-%s' % cur_version)
        )
        self.get_logger().info('Symlink updated')
