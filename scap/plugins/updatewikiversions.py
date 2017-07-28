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
        json_path = utils.get_realm_specific_filename(
            'wikiversions.json', self.config['wmf_realm'],
            self.config['datacenter'])

        db_list_name = os.path.basename(
            os.path.splitext(self.arguments.dblist)[0])

        script = os.path.join(
            self.config['stage_dir'], 'multiversion', 'bin', 'expanddblist')
        dblist = subprocess.check_output([script, db_list_name]).splitlines()

        new_version_dir = 'php-%s' % self.arguments.branch
        new_branch = os.path.join(self.config['stage_dir'], new_version_dir)

        if not os.path.isdir(new_branch):
            raise ValueError('Invalid version specifier: %s' % new_version_dir)

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
            version_rows[dbname] = new_version_dir

        with open(json_path, 'w') as json_out:
            json.dump(version_rows, json_out, ensure_ascii=False, indent=4,
                      separators=(',', ':'), sort_keys=True)

        self.get_logger().info('Updated %s: %s inserted, %s migrated.' %
                               (json_path, inserted, migrated))

        utils.move_symlink(
            os.path.join(self.config['stage_dir'], 'php'),
            new_version_dir
        )
        self.get_logger().info('Symlink updated')
