<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Email\Payment\Pdf;


/**
 * Default implementation of email PDF client.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Common\Client\Summary\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/** client/html/email/payment/pdf/subparts
	 * List of HTML sub-clients rendered within the email payment PDF section
	 *
	 * The output of the frontend is composed of the code generated by the HTML
	 * clients. Each HTML client can consist of serveral (or none) sub-clients
	 * that are responsible for rendering certain sub-parts of the output. The
	 * sub-clients can contain HTML clients themselves and therefore a
	 * hierarchical tree of HTML clients is composed. Each HTML client creates
	 * the output that is placed inside the container of its parent.
	 *
	 * At first, always the HTML code generated by the parent is printed, then
	 * the HTML code of its sub-clients. The order of the HTML sub-clients
	 * determines the order of the output of these sub-clients inside the parent
	 * container. If the configured list of clients is
	 *
	 *  array( "subclient1", "subclient2" )
	 *
	 * you can easily change the order of the output by reordering the subparts:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1", "subclient2" )
	 *
	 * You can also remove one or more parts if they shouldn't be rendered:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1" )
	 *
	 * As the clients only generates structural HTML, the layout defined via CSS
	 * should support adding, removing or reordering content by a fluid like
	 * design.
	 *
	 * @param array List of sub-client names
	 * @since 2020.07
	 * @category Developer
	 */
	private $subPartPath = 'client/html/email/payment/pdf/subparts';
	private $subPartNames = [];


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string HTML code
	 */
	public function getBody( string $uid = '' ) : string
	{
		$view = $this->getView();

		if( $view->extOrderItem->getPaymentStatus() < \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED ) {
			return '';
		}

		$content = '';
		foreach( $this->getSubClients() as $subclient ) {
			$content .= $subclient->setView( $view )->getBody( $uid );
		}
		$view->pdfBody = $content;

		/** client/html/email/payment/pdf/template-body
		 * Relative path to the HTML body template of the email payment PDF client.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the result shown in the body of the e-mail. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in client/html/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "standard" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "standard"
		 * should be replaced by the name of the new class.
		 *
		 * The email payment PDF client allows to use a different template for
		 * each payment status value. You can create a template for each payment
		 * status and store it in the "email/payment/<status number>/" directory
		 * below the "templates" directory (usually in client/html/templates). If no
		 * specific layout template is found, the common template in the
		 * "email/payment/" directory is used.
		 *
		 * @param string Relative path to the template creating code for the HTML e-mail body
		 * @since 2020.07
		 * @category Developer
		 * @see client/html/email/payment/pdf/template-header
		 */
		$tplconf = 'client/html/email/payment/pdf/template-body';

		$view->pdf = new Tcpdf( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
		$view->pdf->setCreator( PDF_CREATOR );
		$view->pdf->setAuthor( 'Aimeos' );

		// Generate HTML before creating first PDF page to include header added in template
		$content = $view->render( $view->config( $tplconf, 'email/payment/pdf-body-standard' ) );

		$view->pdf->addPage();
		$view->pdf->writeHtml( $content );
		$view->pdf->lastPage();

		$view->mail()->addAttachment( $view->pdf->output( '', 'S' ), 'application/pdf', 'order_' . $view->extOrderItem->getId() . '.pdf' );

		return '';
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return \Aimeos\Client\Html\Iface Sub-client object
	 */
	public function getSubClient( string $type, string $name = null ) : \Aimeos\Client\Html\Iface
	{
		/** client/html/email/payment/pdf/decorators/excludes
		 * Excludes decorators added by the "common" option from the "email payment pdf" html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "client/html/common/decorators/default" before they are wrapped
		 * around the html client.
		 *
		 *  client/html/email/payment/pdf/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\Client\Html\Common\Decorator\*") added via
		 * "client/html/common/decorators/default" to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2020.07
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/email/payment/pdf/decorators/global
		 * @see client/html/email/payment/pdf/decorators/local
		 */

		/** client/html/email/payment/pdf/decorators/global
		 * Adds a list of globally available decorators only to the "email payment html" html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\Client\Html\Common\Decorator\*") around the html client.
		 *
		 *  client/html/email/payment/pdf/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\Client\Html\Common\Decorator\Decorator1" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2020.07
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/email/payment/pdf/decorators/excludes
		 * @see client/html/email/payment/pdf/decorators/local
		 */

		/** client/html/email/payment/pdf/decorators/local
		 * Adds a list of local decorators only to the "email payment html" html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\Client\Html\Checkout\Decorator\*") around the html client.
		 *
		 *  client/html/email/payment/pdf/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\Client\Html\Checkout\Decorator\Decorator2" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2020.07
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/email/payment/pdf/decorators/excludes
		 * @see client/html/email/payment/pdf/decorators/global
		 */

		return $this->createSubClient( 'email/payment/pdf/' . $type, $name );
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of HTML client names
	 */
	protected function getSubClientNames() : array
	{
		return $this->getContext()->getConfig()->get( $this->subPartPath, $this->subPartNames );
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param \Aimeos\MW\View\Iface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return \Aimeos\MW\View\Iface Modified view object
	 */
	public function addData( \Aimeos\MW\View\Iface $view, array &$tags = [], string &$expire = null ) : \Aimeos\MW\View\Iface
	{
		$basket = $view->extOrderBaseItem;

		// we can't cache the calculation because the same client object is used for all e-mails
		$view->summaryCostsDelivery = $this->getCostsDelivery( $basket );
		$view->summaryCostsPayment = $this->getCostsPayment( $basket );
		$view->summaryNamedTaxes = $this->getNamedTaxes( $basket );
		$view->summaryTaxRates = $this->getTaxRates( $basket );
		$view->summaryBasket = $basket;

		if( $view->extOrderItem->getPaymentStatus() >= $this->getDownloadPaymentStatus() ) {
			$view->summaryShowDownloadAttributes = true;
		}

		return parent::addData( $view, $tags, $expire );
	}
}
