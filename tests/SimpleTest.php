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
        $ref = new ReferenceTest;
        $ref->name = "foobar";
        $conn->Save($ref);

        $ref = new ReferenceTest;
        $ref->id   = 9988;
        $ref->name = "barfoo";
        $conn->Save($ref);

        $admin = new \crodas\QuickAdmin\QuickAdmin($conn, 'foobar');
        $view = $admin->handleCreate([], '/foobar-url');
        $this->assertTrue(is_string($view));
        $this->assertTrue(preg_match('/type=.file./', $view) > 0);
        $this->assertTrue(preg_match('/foobar-url/', $view) > 0);
        $this->assertTrue(preg_match('/alert-danger/', $view) == 0);
        $this->assertTrue(preg_match('/barfoo/', $view) == 1);
        $this->assertTrue(preg_match('/foobar/', $view) == 1);
        $this->assertTrue(preg_match('/foobar\[xreference\]\[_id\]/', $view) == 1);
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
            'rel' => ['name' => 'wakawaka'],
            'xreference' => ['_id' => '9988'],
        ]], '/foobar-url');
        $this->assertTrue($view);
        $doc = $conn->foobar->findOne();
        $this->assertTrue(!is_null($doc));
        $this->assertTrue($doc->xreference instanceof \ActiveMongo2\Reference);
        $this->assertTrue($doc->xreference->getObject() instanceof \ReferenceTest);
        $this->assertEquals($doc->xreference->getObject()->id, 9988);
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
        $this->assertTrue(preg_match('/9988.+selected/', $view) > 0);
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

    /**
     *  @dependsOn testUpdateSuccess
     */
    public function testList()
    {
        $conn  = get_conn();
        $admin = new \crodas\QuickAdmin\QuickAdmin($conn, 'foobar');
        $view  = $admin->handleList('/');
        $this->assertTrue(preg_match('/xxx@yyy.com/', $view) > 0);
    }

}
