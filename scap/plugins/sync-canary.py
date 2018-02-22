# -*- coding: utf-8 -*-
"""
    scap.plugins.synccanary
    ~~~~~~~~~~~~~~~~~~~~~~~

    Sync (scap pull) canary hosts
    
    Copyright © 2014-2017 Wikimedia Foundation and Contributors.

    This file is part of Scap.

    Scap is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, version 3.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
"""

from __future__ import absolute_import
from __future__ import print_function

import socket

from scap.main import AbstractSync
import scap.cli as cli
import scap.log as log
import scap.ssh as ssh
import scap.lock as lock


@cli.command('sync-canary')
class SyncCanary(AbstractSync):
    """Sync specific canary hosts."""

    @cli.argument('--force', action='store_true', help='Skip canary checks')
    @cli.argument('-h', '--host', default=None, action='append',
                  help='host name to sync, defaults to all canary hosts.')
    @cli.argument('message', nargs='*', help='Log message for SAL')
    def main(self, *extra_args):
        self._check_sync_flag()
        full_target_list = self._get_target_list()

        hosts = self.arguments.host

        if hosts is None:
            hosts = self._get_canary_list()

        canaries = [node for node in hosts
                    if node in full_target_list]
        if not canaries:
            errmsg = "host argument(s) did not match any valid targets."
            raise ValueError(errmsg)

        with lock.Lock(self.get_lock_file(), self.arguments.message):

            synchosts = ", ".join(canaries)
            self.get_logger().info("Syncing canaries: %s", (synchosts))

            self._before_cluster_sync()
            self._sync_common()
            self._after_sync_common()
            self._sync_masters()

            sync_cmd = self._apache_sync_command(
                self._get_master_list())
            sync_cmd.append(socket.getfqdn())
            update_canaries = ssh.Job(
                canaries,
                user=self.config['ssh_user'],
                key=self.get_keyholder_key())
            update_canaries.command(sync_cmd)
            update_canaries.progress(
                log.reporter(
                    'sync-canaries',
                    self.config['fancy_progress']))
            succeeded, failed = update_canaries.run()

            if failed:
                self.get_logger().warning(
                    '%d canaries had sync errors', failed)
                self.soft_errors = True
