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
        $view = $admin->handleCreate(['foobar' => ['email' => 'xxx']], '/foobar-url');
        $this->assertTrue(is_string($view));
        $this->assertTrue(preg_match('/foobar-url/', $view) > 0);
        $this->assertTrue(preg_match('/alert-danger/', $view) > 0);
        $this->assertTrue(preg_match('/xxx/', $view) > 0);
    }

    public function testCreateSuccess()
    {
        $conn  = get_conn();
        $admin = new \crodas\QuickAdmin\QuickAdmin($conn, 'foobar');
        $view = $admin->handleCreate(['foobar' => [
            'email' => 'xxx@foobar.com', 'first_name' => 'xxx',
            'rel' => ['name' => 'wakawaka']]], '/foobar-url');
        $this->assertTrue($view);
        $this->assertTrue(!is_null( $conn->foobar->findOne() ));
    }

    /**
     *  @dependsOn testUpdateShow
     */
    public function testUpdateShowError()
    {
        $conn  = get_conn();
        $admin = new \crodas\QuickAdmin\QuickAdmin($conn, 'foobar');
        $doc   = $conn->foobar->findOne();
        $view = $admin->handleUpdate($doc, ['foobar' => ['email' => 'xxx']], '/foobar-url');
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
        $this->assertTrue(preg_match('/wakawaka/', $view) > 0);
    }

    /**
     *  @dependsOn testUpdateShowError
     */
    public function testUpdateSuccess()
    {
        $conn  = get_conn();
        $admin = new \crodas\QuickAdmin\QuickAdmin($conn, 'foobar');
        $doc   = $conn->foobar->findOne();
        $view = $admin->handleUpdate($doc, ['foobar' => ['email' => 'xxx@yyy.com', 'rel' => ['name' => 'yyy']]], '/foobar-url');
        $this->assertTrue($view);
        $this->assertEquals($doc->rel->name, 'yyy');
        $doc   = $conn->foobar->findOne();
        $this->assertEquals($doc->rel->name, 'yyy');
    }

}
