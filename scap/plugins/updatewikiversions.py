"""
Updates wiki versions json files and symlink pointers
"""

import json

import scap.utils as utils


@cli.command('update-wikiversions')
class UpdateWikiversions(cli.Application):
    """ Scap subcommand for updating wikiversions.json to a new version"""

    @cli.argument('dblist',
                  help='The dblist file to use as input for migrating.')
    @cli.argument('branch', help='The name of the branch to migrate to.')
    def main(self, *extra_args):
        json_path = utils.get_realm_specific_filename('wikiversions.json',
                                                      self.config['wmf_realm'],
                                                      self.config['datacenter']
                                                      )
        db_list_name = os.path.basename(
            os.path.splitext(self.arguments.dblist)[0])

        # FIX THIS ITS NOT EVEN PYTHON LOL YOU DOOF
        dblist = MWWikiversions::readDbListFile(db_list_name)

        new_version = self.arguments.branch
        new_branch = os.path.join(self.config['stage_dir'],
                                  'php-%s' % new_version)

        if not os.path.isdir(new_branch):
            raise ValueError('Invalid version specifier: %s' % new_version)

        if os.path.exists(json_path):
            # FIX THIS ITS NOT EVEN PYTHON LOL YOU DOOF
            version_rows = MWWikiversions::readWikiVersionsFile(json_path)
        else:
            if db_list_name is not 'all':
                raise RuntimeError(
                    'No %s file and not invoked with "all."' % json_path +
                    'Cowardly refusing to act.'
                )
            self.get_logger().info('%s not found -- rebuilding from scratch!' %
                                   json_path)
            version_rows = []

        inserted = 0
        migrated = 0
        version_rows = []

        for dbname in dblist:
            if dbname in version_rows:
                inserted++
            else:
                migrated++
            version_rows[dbname] = new_version

        json.dump(version_rows, ensure_ascii=False, indent=4,
                  separators=(',', ':'))
        self.get_logger().info('Updated %s: %s inserted, %s migrated.' %
                               (json_path, inserted, migrated))
