<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Acelle\Model\SendingServer;
use Acelle\Model\User;

class SendingServerPolicy
{
    use HandlesAuthorization;

    public function read(User $user, SendingServer $item, $role)
    {
        switch ($role) {
            case 'admin':
                $can = $user->admin->getPermission('sending_server_read') != 'no';
                break;
            case 'customer':
                $can = $user->customer->getOption('sending_server_option') == \Acelle\Model\Plan::SENDING_SERVER_OPTION_OWN;
                break;
        }

        return $can;
    }

    public function readAll(User $user, SendingServer $item, $role)
    {
        switch ($role) {
            case 'admin':
                $can = $user->admin->getPermission('sending_server_read') == 'all';
                break;
            case 'customer':
                $can = false;
                break;
        }

        return $can;
    }

    public function create(User $user, $role)
    {
        switch ($role) {
            case 'admin':
                $can = $user->admin->getPermission('sending_server_create') == 'yes';

                // config/limit.php
                $limit = app_profile('sending_server.limit');
                if (!is_null($limit)) {
                    $sendingServerCount = SendingServer::system()->count();
                    $can = $can && ($sendingServerCount < $limit);
                } else {
                    // ignore limit because it is null
                }

                break;
            case 'customer':
                // init
                $can = true;
                $useOwnServer = $user->customer->getOption('sending_server_option') == \Acelle\Model\Plan::SENDING_SERVER_OPTION_OWN;

                //
                if ($useOwnServer) {
                    // check max campaigns
                    $max = $user->customer->getOption('sending_servers_max');
                    $isUnlimited = $max == -1; // -1 means unlimited
                    $notReachMax = $user->customer->sendingServersCount() < $max;
                    $can = $isUnlimited || $notReachMax; // check max campaigns: unlimited or is not reach max

                // check if user can create server type
                // $can = $can && $user->customer->isAllowCreateSendingServerType($item->type);
                } else {
                    // use system server than do not check max
                }

                // config/limit.php
                $limit = app_profile('sending_server.limit');
                if (!is_null($limit)) {
                    $sendingServerCount = $user->customer->sendingServers()->count();
                    $can = $can && ($sendingServerCount < $limit);
                } else {
                    // ignore limit because it is null
                }

                break;
        }

        return $can;
    }

    public function update(User $user, SendingServer $item, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('sending_server_update');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $item->admin_id);
                break;
            case 'customer':
                $can = $user->customer->getOption('sending_server_option') == \Acelle\Model\Plan::SENDING_SERVER_OPTION_OWN
                        && $user->customer->id == $item->customer_id;
                break;
        }

        return $can;
    }

    public function delete(User $user, SendingServer $item, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('sending_server_delete');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $item->admin_id);
                break;
            case 'customer':
                $can = $user->customer->getOption('sending_server_option') == \Acelle\Model\Plan::SENDING_SERVER_OPTION_OWN
                    && $user->customer->id == $item->customer_id;
                break;
        }

        return $can;
    }

    public function disable(User $user, SendingServer $item, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('sending_server_update');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $item->admin_id);
                break;
            case 'customer':
                $can = $user->customer->getOption('sending_server_option') == \Acelle\Model\Plan::SENDING_SERVER_OPTION_OWN
                    && $user->customer->id == $item->customer_id;
                break;
        }

        return $can && $item->status != "inactive";
    }

    public function enable(User $user, SendingServer $item, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('sending_server_update');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $item->admin_id);
                break;
            case 'customer':
                $can = $user->customer->getOption('sending_server_option') == \Acelle\Model\Plan::SENDING_SERVER_OPTION_OWN
                    && $user->customer->id == $item->customer_id;
                break;
        }

        return $can && $item->status != "active";
    }

    public function test(User $user, SendingServer $item, $role)
    {
        return $this->update($user, $item, $role) || !isset($item->id);
    }
}
