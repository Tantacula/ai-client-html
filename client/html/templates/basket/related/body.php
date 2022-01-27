<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

$enc = $this->encoder();


?>
<section class="aimeos basket-related" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ) ?>">

	<?php if( isset( $this->relatedErrorList ) ) : ?>
		<ul class="error-list">
			<?php foreach( (array) $this->relatedErrorList as $errmsg ) : ?>
				<li class="error-item"><?= $enc->html( $errmsg ) ?></li>
			<?php endforeach ?>
		</ul>
	<?php endif ?>


	<h1><?= $enc->html( $this->translate( 'client', 'Related' ), $enc::TRUST ) ?></h1>

	<?= $this->block()->get( 'basket/related/bought' ) ?>

</section>
