<?php

namespace EdmondsCommerce\BehatPrestashop;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

class PrestaShopAdminContext extends RawMinkContext implements Context, SnippetAcceptingContext
{
    public $adminUserName;
    public $adminPassword;
    public $adminPath;

    public function __construct($adminUser, $adminPassword, $adminPath)
    {
        $this->adminUserName = $adminUser;
        $this->adminPassword = $adminPassword;
        $this->adminPath = $adminPath;
    }


    /**
     * @Then /^I go to the Prestashop Admin Page/
     */
    public function iGotoAdminAndLogin()
    {
        $this->visitPath('/'.$this->adminPath.'/index.php');
        $text = $this->getSession()->getPage()->getHtml();

        if(preg_match('/\<button name="submitLogin"/', $text) > 0) {
            //log in
            $this->getSession()->getPage()->fillField('email', $this->adminUserName);
            $this->getSession()->getPage()->fillField('passwd', $this->adminPassword);

            $this->getSession()->getPage()->find('css', 'button[name="submitLogin"]')->click();
            $this->getSession()->wait(9000);
        }
    }

    /**
     * @Then /^(?:|I )go to order with reference of "(?P<text>(?:[^"]|\\")*)"$/
     */
    public function iGoToOrderInAdmin($orderReference)
    {
        $this->getSession()->getPage()->find('css', '#maintab-AdminParentOrders')->click();
        $this->getSession()->wait(2000);
        //'table.order td:contains("QMIQISOSG")'
        $this->getSession()->getPage()->find('css', 'table.order td:contains("'.$orderReference.'")')->click();
        $this->getSession()->wait(2000);

        $text = $this->getSession()->getPage()->getHtml();

        if(!preg_match('/Order\s'.$orderReference.'/', $text)) {
            throw new \Exception('Could not determine if I am on the order page');
        }
    }

    /**
     * @Then /^(?:|I )click on Prestashop close on any prompt$/
     */
    public function iClickOnPrestashopOkPrompt()
    {
        $box = $this->getSession()->getPage()->findAll('css','.modal-dialog .alert');

        if(count($box) > 0) {
            $this->getSession()->getPage()->find('css','.modal-dialog .alert button[data-dismiss="modal"]')->click();
            $this->getSession()->wait(1000);
        }
    }

    /**
     * @Then /^(?:|I )log out of the Prestashop admin/
     */
    public function logoutofThePrestashopAdmin()
    {
        $logoutLink = $this->getSession()->getPage()->find('css', 'a#header_logout');
        $logoutLinkPath = $logoutLink->getAttribute('href');
        $this->visitPath('/'.$this->adminPath.'/'.$logoutLinkPath);
        $url = $this->getSession()->getCurrentUrl();

        if(stripos($url, 'AdminLogin') === false) {
            throw new \Exception('could not log out of the admin');
        }
    }

}