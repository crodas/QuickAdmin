<?php

use ActiveMongo2\Tests\Document\AutoincrementDocument;
use ActiveMongo2\Tests\Document\UserDocument;
use ActiveMongo2\Tests\Document\PostDocument;
use ActiveMongo2\Tests\Document\AddressDocument;

class SimpleTest extends \phpunit_framework_testcase
{
    public function testCreateShow()
    {
        $conn  = get_conn();
        $admin = new \crodas\QuickAdmin\QuickAdmin($conn, 'foobar');
        $view = $admin->handleCreate([], '/foobar-url');
        $this->assertTrue(is_string($view));
        $this->assertTrue(preg_match('/foobar-url/', $view) > 0);
        $this->assertTrue(preg_match('/alert-danger/', $view) == 0);
    }

    public function testCreateError()
    {
        $conn  = get_conn();
        $admin = new \crodas\QuickAdmin\QuickAdmin($conn, 'foobar');
        $view = $admin->handleCreate(['email' => 'xxx'], '/foobar-url');
        $this->assertTrue(is_string($view));
        $this->assertTrue(preg_match('/foobar-url/', $view) > 0);
        $this->assertTrue(preg_match('/alert-danger/', $view) > 0);
    }

    public function testCreateSuccess()
    {
        $conn  = get_conn();
        $admin = new \crodas\QuickAdmin\QuickAdmin($conn, 'foobar');
        $view = $admin->handleCreate(['email' => 'xxx@foobar.com', 'first_name' => 'xxx'], '/foobar-url');
        $this->assertTrue($view);
    }

    /**
     *  @dependsOn testUpdateShow
     */
    public function testUpdateShowError()
    {
        $conn  = get_conn();
        $admin = new \crodas\QuickAdmin\QuickAdmin($conn, 'foobar');
        $doc   = $conn->foobar->findOne();
        $view = $admin->handleUpdate($doc, ['email' => 'xxx'], '/foobar-url');
        $this->assertTrue(is_string($view));
        $this->assertTrue(preg_match('/foobar-url/', $view) > 0);
        $this->assertTrue(preg_match('/alert-danger/', $view) > 0);
        $this->assertTrue(preg_match('/xxx/', $view) > 0);
        $this->assertTrue(preg_match('/xxx@foobar.com/', $view) == 0);
    }

    /**
     *  @dependsOn testCreateSuccess
     */
    public function testUpdateShow()
    {
        $conn  = get_conn();
        $admin = new \crodas\QuickAdmin\QuickAdmin($conn, 'foobar');
        $doc   = $conn->foobar->findOne();
        $view = $admin->handleUpdate($doc, [], '/foobar-url');
        $this->assertTrue(is_string($view));
        $this->assertTrue(preg_match('/foobar-url/', $view) > 0);
        $this->assertTrue(preg_match('/alert-danger/', $view) == 0);
        $this->assertTrue(preg_match('/xxx@foobar.com/', $view) > 0);
    }

    /**
     *  @dependsOn testUpdateShowError
     */
    public function testUpdateSuccess()
    {
        $conn  = get_conn();
        $admin = new \crodas\QuickAdmin\QuickAdmin($conn, 'foobar');
        $doc   = $conn->foobar->findOne();
        $view = $admin->handleUpdate($doc, ['email' => 'xxx@yyy.com'], '/foobar-url');
        $this->assertTrue($view);
    }

}
