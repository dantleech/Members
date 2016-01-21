<?php

namespace Bolt\Extension\Bolt\Members;

use Doctrine\DBAL\Connection;
use Silex\Application;

/**
 * Member interface class
 *
 * Copyright (C) 2014-2016 Gawain Lynch
 *
 * @author    Gawain Lynch <gawain.lynch@gmail.com>
 * @copyright Copyright (c) 2014-2016, Gawain Lynch
 * @license   https://opensource.org/licenses/MIT MIT
 */
class Members
{
    /** @var Application */
    private $app;
    /** @var Records */
    private $records;
    /** @var Authenticate */
    private $authenticate;
    /** @var array */
    private $config;
    /** @var array */
    private $roles = [];

    /**
     * Constructor.
     *
     * @param Application $app
     * @param array       $config
     */
    public function __construct(Application $app, array $config)
    {
        $this->app = $app;
        $this->config = $config;
        $this->records = $this->app['members.records'];
        $this->authenticate = $app['members.authenticate'];
    }

    /**
     * Roles currently set
     *
     * @return array
     */
    public function getAvailableRoles()
    {
        return $this->roles;
    }

    /**
     * Add a role
     *
     * @param string $role The internal name for the role
     * @param string $name The user friendly name for the role
     */
    public function addAvailableRole($role, $name = '')
    {
        if ($name == '') {
            $name = $role;
        }

        try {
            $this->roles[$role] = $name;
        } catch (\Exception $e) {
        }
    }

    /**
     * Get a member record
     *
     * @param string $field The user field to lookup the user by (id, username or email)
     * @param string $value Lookup value
     *
     * @return array|boolean
     */
    public function getMember($field, $value)
    {
        if (! empty($field) && ! empty($value)) {
            $record = $this->records->getMember($field, $value);
            if ($record) {
                return $record;
            }
        }

        return false;
    }

    /**
     * Get a member's meta records
     *
     * @param integer $id   The user's ID
     * @param string  $meta Optional meta value to limit to
     *
     * @return array|boolean
     */
    public function getMemberMeta($id, $meta = false)
    {
        $records = $this->records->getMemberMeta($id, $meta);

        if ($records) {
            return $records;
        }

        return false;
    }

    /**
     * Update/insert a member record in the database
     *
     * @param int   $userid
     * @param array $values
     *
     * @return boolean
     */
    public function updateMember($userid, $values)
    {
        return $this->records->updateMember($userid, $values);
    }

    /**
     * Add/update a member's meta record
     *
     * @param int    $userid
     * @param string $meta
     * @param string $value
     *
     * @return boolean
     */
    public function updateMemberMeta($userid, $meta, $value)
    {
        return $this->records->updateMemberMeta($userid, $meta, $value);
    }

    /**
     * Test a member record to see if they have a specific role
     *
     * @param string $field The user field to lookup the user by (id, username or email)
     * @param string $value Lookup value
     * @param string $role  The role to test
     *
     * @return boolean
     */
    public function hasRole($field, $value, $role)
    {
        $member = $this->getMember($field, $value);

        if (is_array($member['roles']) && in_array($role, $member['roles'])) {
            return true;
        }

        return false;
    }

    /**
     * Get a set of members record from the database
     *
     * @return boolean|array
     */
    protected function getMembers()
    {
        return $this->records->getMembers();
    }
}
