<?php

namespace EdmondsCommerce\BehatPrestashop;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

class PrestaShopFrontEndContext extends RawMinkContext implements Context, SnippetAcceptingContext
{
    private $guesser;
    private $name;
    private $faker;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        date_default_timezone_set('Europe/London');
        $this->faker = \Faker\Factory::create();

        $this->faker->addProvider(new Faker\Provider\en_GB\PhoneNumber($this->faker));
        $this->guesser = new EdmondsCommerce\FakerContext\Guesser(\Faker\Factory::create());
    }

    /**
     * @Then I add a product to the cart
     */
    public function addAProductToTheCart()
    {
        $selector = '.button-container .ajax_add_to_cart_button';
        $elements = $this->getSession()->getPage()->findAll("css", $selector);
        if(count($elements) == 0){
            throw new \Exception("Product could not be found");
        }

        /**
         * @var Behat\Mink\Element\Element $element
         */
        $element = current($elements);

        $element->click();
    }

    /**
     * @Then I fill in an email address on the user registration
     */
    public function fillInEmailAddress()
    {
        $email = $this->faker->email;
        $this->email = $email;

        $field = 'email_create';
        $this->getSession()->getPage()->fillField($field, $email);

        $this->getSession()->getPage()->find('css','#SubmitCreate')->click();
    }

    /**
     * @Then /^I wait for cart popup to load/
     */
    public function iWaitForCartPopupToLoad()
    {
        $this->getSession()->wait(5000);
    }

    /**
     * @Then /^I wait for cart transition/
     */
    public function iWaitForCartAjaxTransition()
    {
        $this->getSession()->wait(5000);
    }

    /**
     * @Then /^I wait for page redirect/
     */
    public function iWaitForPageRedirect()
    {
        $this->getSession()->wait(10000);
    }

    /**
     * @Then I wait for the document ready event
     * @Then I wait for the page to fully load
     */
    public function iWaitForDocumentReady()
    {
        $this->getSession()->wait(10000, '("complete" === document.readyState)');
    }

    /**
     * @Then /^I wait for page to load/
     */
    public function iWaitForPageToLoad()
    {
        $this->getSession()->wait(9000);
    }

    /**
     * @Then /^I fill in the personal information/
     */
    public function iFillPersonalInformation()
    {
        $generator = new Faker\Provider\Internet($this->faker);

        $customerFirstname  = $this->faker->firstName;
        $customerLastname = $this->faker->firstName;

        $this->name = $customerFirstname.' '.$customerLastname;

        $password = $generator->password(6,10);

        $this->getSession()->getPage()->fillField('email', $this->email);
        $this->getSession()->getPage()->fillField('customer_firstname', $customerFirstname);
        $this->getSession()->getPage()->fillField('customer_lastname', $customerFirstname);
        $this->getSession()->getPage()->fillField('passwd', $password);
        $this->getSession()->getPage()->find('css','#uniform-id_gender1')->click();
    }

    /**
     * @Then /^I fill in the address form/
     */
    public function iFillInTheAddressForm()
    {
        $address1 = $this->faker->streetAddress;
        $city = $this->faker->city;
        $postcode = $this->faker->postcode;
        $phoneNumber = $this->faker->phoneNumber;

        $this->getSession()->getPage()->fillField('address1', $address1);
        $this->getSession()->getPage()->fillField('city', $city);
        $this->getSession()->getPage()->fillField('postcode', $postcode);
        $this->getSession()->getPage()->fillField('phone', $phoneNumber);
    }

    /**
     * @Then /^I press the checkout button/
     */
    public function iPressTheCheckoutButton()
    {
        $this->getSession()->getPage()->find('css','.standard-checkout')->click();
    }

}