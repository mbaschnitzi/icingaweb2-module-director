<?php

namespace Icinga\Module\Director\Ddo;


use Icinga\Data\Db\DbConnection;

class DdoDb extends DbConnection
{
    public function isPgsql()
    {
        // TODO: not yet
        return false;
    }
}