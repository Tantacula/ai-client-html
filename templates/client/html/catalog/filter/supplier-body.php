<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2022
 */

$enc = $this->encoder();

$linkKey = $this->param( 'f_catid' ) ? 'client/html/catalog/tree/url' : 'client/html/catalog/lists/url';


?>
<?php $this->block()->start( 'catalog/filter/supplier' ) ?>
	<section class="catalog-filter-supplier">
		<h2><?= $enc->html( $this->translate( 'client', 'Suppliers' ), $enc::TRUST ) ?></h2>

		<div class="supplier-lists">
			<?php if( $this->param( 'f_supid' ) ) : ?>
				<a class="btn supplier-selected" href="<?= $enc->attr( $this->link( $linkKey, $this->get( 'supplierResetParams', [] ) ) ) ?>">
					<?= $enc->html( $this->translate( 'client', 'Reset' ), $enc::TRUST ) ?>
				</a>
			<?php endif ?>

			<input class="form-control search"
				name="<?= $enc->attr( $this->formparam( ['supplier', 'search'] ) ) ?>"
				placeholder="<?= $enc->attr( $this->translate( 'client', 'Search' ) ) ?>"
			/>

			<fieldset>
				<ul class="attr-list">

					<?php foreach( $this->get( 'supplierList', [] ) as $id => $supplier ) : ?>
						<li class="attr-item" data-id="<?= $enc->attr( $id ) ?>">

							<input class="attr-item" type="checkbox"
								id="sup-<?= $enc->attr( $id ) ?>"
								name="<?= $enc->attr( $this->formparam( ['f_supid', ''] ) ) ?>"
								value="<?= $enc->attr( $id ) ?>"
								<?= ( in_array( $id, (array) $this->param( 'f_supid', [] ) ) ? 'checked="checked"' : '' ) ?>
							>

							<label class="attr-name" for="sup-<?= $enc->attr( $id ) ?>">
								<div class="media-list">

									<?php foreach( $supplier->getRefItems( 'media', 'icon', 'default' ) as $mediaItem ) : ?>
										<?= $this->partial(
											$this->config( 'client/html/common/partials/media', 'common/partials/media' ),
											array( 'item' => $mediaItem, 'boxAttributes' => array( 'class' => 'media-item' ) )
										) ?>
									<?php endforeach ?>

								</div>
								<span><?= $enc->html( $supplier->getName(), $enc::TRUST ) ?></span>
							</label>
						</li>
					<?php endforeach ?>

					<li class="attr-item prototype" data-id="">
						<input class="attr-item" type="checkbox" id="" value="" name="<?= $enc->attr( $this->formparam( ['f_supid', ''] ) ) ?>">
						<label class="attr-name" for="">
							<div class="media-list">img src="" title=""</div>
							<span></span>
						</label>
					</li>

				</ul>
			</fieldset>
		</div>

		<?php if( $this->config( 'client/html/catalog/filter/button', true ) ) : ?>
			<noscript>
				<button class="filter btn btn-primary" type="submit">
					<?= $enc->html( $this->translate( 'client', 'Show' ), $enc::TRUST ) ?>
				</button>
			</noscript>
		<?php endif ?>

	</section>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'catalog/filter/supplier' ) ?>
