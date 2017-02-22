<?php

namespace RelationalExample\Model;

use Illuminate\Database\Capsule\Manager as Capsule;

class User
{
    public function get($username = null)
    {
        $query = Capsule::table('user')->select(['id', 'user_name']);

        if ($username !== null) {
            $query->where('user_name', '=', $username);
        }

        $result = $query->get();

        if (count($result) > 0) {
            return $result;
        }

        return;
    }
}
