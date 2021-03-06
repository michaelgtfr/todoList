<?php
/**
 * Created by PhpStorm.
 * User: mickd
 * Date: 04/05/2020
 * Time: 11:58
 */

namespace App\Tests\Controller;

use App\DataFixtures\AdminUserFixture;
use App\Entity\User;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class ListTaskControllerTest extends WebTestCase
{
    use FixturesTrait;

    protected $client = null;

    protected function dataFixture()
    {
        $this->loadFixtures([
            AdminUserFixture::class,
        ]);
    }

    public function testListTaskLoggedIn()
    {
        $this->client = static::createClient();
        $this->dataFixture();
        $this->logIn();

        $crawler = $this->client->request('GET', '/tasks');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1,$crawler->filter('.tasks')->count());
    }

    /**
     * allows connection to the application
     */
    public function logIn()
    {
        $session = self::$container->get('session');

        $user = self::$container->get('doctrine')->getRepository(User::class)->findOneBy(['username' => 'username_test']);

        $firewallName = 'main';

        $firewallContext = 'main';

        $token = new PostAuthenticationGuardToken($user, $firewallName, $user->getRoles());
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}