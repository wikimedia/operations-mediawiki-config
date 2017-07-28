"""
Updates wiki versions json files and symlink pointers
"""

import json
import subprocess

import scap.tasks as tasks
import scap.utils as utils


@cli.command('update-wikiversions')
class UpdateWikiversions(cli.Application):
    """ Scap subcommand for updating wikiversions.json to a new version"""

    @cli.argument('dblist',
                  help='The dblist file to use as input for migrating.')
    @cli.argument('branch', help='The name of the branch to migrate to.')
    def main(self, *extra_args):
        self.update_wikiversions()
        self.update_branch_pointer()

    def update_wikiversions(self):
        json_path = utils.get_realm_specific_filename('wikiversions.json',
                                                      self.config['wmf_realm'],
                                                      self.config['datacenter']
                                                      )
        db_list_name = os.path.basename(
            os.path.splitext(self.arguments.dblist)[0])

        # FIX THIS ITS NOT EVEN PYTHON LOL YOU DOOF
        # dblist = MWWikiversions::readDbListFile(db_list_name)

        new_version = self.arguments.branch
        new_branch = os.path.join(self.config['stage_dir'],
                                  'php-%s' % new_version)

        if not os.path.isdir(new_branch):
            raise ValueError('Invalid version specifier: %s' % new_version)

        if os.path.exists(json_path):
            # FIX THIS ITS NOT EVEN PYTHON LOL YOU DOOF
            # version_rows = MWWikiversions::readWikiVersionsFile(json_path)
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

        with open(json_path, 'w') as json_out:
            json.dump(version_rows, json_out, ensure_ascii=False, indent=4,
                    separators=(',', ':'))

        self.get_logger().info('Updated %s: %s inserted, %s migrated.' %
                               (json_path, inserted, migrated))


    def update_branch_pointer():
    	branch_dirs = tasks.get_wikiversions_ondisk()

        if not branch_dirs:
            raise RuntimeError('No deployment branch directories found')

    	# Default sort is lexographical which incorrectly sorts e.g. [1.23wmf9, 1.23wmf10]
        # FIX THIS ITS NOT EVEN PYTHON LOL YOU DOOF
    	usort( $branchDirs, 'version_compare' );
    	$current = array_pop( $branchDirs );

    	# Symlink common/php to the current branch using a relative path so that
    	# the symlink works from both MEDIAWIKI_STAGING_DIR and
    	# MEDIAWIKI_DEPLOYMENT_DIR
        # FIX THIS ITS NOT EVEN PYTHON LOL YOU DOOF
    	php_link_target = substr( $current, strlen( MEDIAWIKI_DEPLOYMENT_DIR ) + 1 );
    	self.update_symlink(
            os.path.join(self.config['staging_dir'], 'php'), php_link_target)

    def update_symlink(self, link, dest):
    	// Change pwd to MEDIAWIKI_STAGING_DIR so that relative paths can be verified
        with utils.cd(self.config['staging_dir']):

            if not os.path.exists(dest):
                raise IOError('link target %s does not exist' % dest)

            if os.path.exists(link):
                if os.path.realpath(link) == os.path.realpath(dest):
                    self.get_logger().info('%s is already up-to-date', link)
        		    return

                if not os.path.islink(link):
                    raise IOError('%s exists and is not a symbolic link' %
                                  link)

                if not os.unlink(link)
                    raise IOError('failed to unlink %s' % link)

            if not os.link(dest, link):
                raise IOError('failed to create %s' % link)

        	self.get_logger().info('%s => %s' % (link, dest))
