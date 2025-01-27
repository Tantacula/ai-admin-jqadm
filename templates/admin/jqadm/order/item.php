<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2023
 */

$selected = function( $key, $code ) {
	return ( $key === $code ? 'selected="selected"' : '' );
};


$enc = $this->encoder();

$searchParams = $params = $this->get( 'pageParams', [] );
unset( $searchParams['id'] );

/// Price format with price value (%1$s) and currency (%2$s)
$priceFormat = $this->translate( 'client/code', '%1$s %2$s' );

$serviceAttrCodes = [
	/** admin/jqadm/order/service/delivery/attribute/suggest
	 * List of suggested configuration keys for delivery service attributes in orders
	 *
	 * Service attributes in orders can store arbitrary key value pairs. This
	 * setting gives editors a hint which config keys are available and are used
	 * in the templates.
	 *
	 * @param string List of suggested config keys
	 * @since 2017.10
	 * @category Developer
	 * @see admin/jqadm/order/service/payment/attribute/suggest
	 */
	'delivery' => $this->config( 'admin/jqadm/order/service/delivery/attribute/suggest', ['trackingid'] ),

	/** admin/jqadm/order/service/payment/attribute/suggest
	 * List of suggested configuration keys for payment service attributes in orders
	 *
	 * Service attributes in orders can store arbitrary key value pairs. This
	 * setting gives editors a hint which config keys are available and are used
	 * in the templates.
	 *
	 * @param string List of suggested config keys
	 * @since 2017.10
	 * @category Developer
	 * @see admin/jqadm/order/service/delivery/attribute/suggest
	 */
	'payment' => $this->config( 'admin/jqadm/order/service/payment/attribute/suggest', [] ),
];

$deliveryStatusList = [
	'-1' => $this->translate( 'mshop/code', 'stat:-1' ),
	'0' => $this->translate( 'mshop/code', 'stat:0' ),
	'1' => $this->translate( 'mshop/code', 'stat:1' ),
	'2' => $this->translate( 'mshop/code', 'stat:2' ),
	'3' => $this->translate( 'mshop/code', 'stat:3' ),
	'4' => $this->translate( 'mshop/code', 'stat:4' ),
	'5' => $this->translate( 'mshop/code', 'stat:5' ),
	'6' => $this->translate( 'mshop/code', 'stat:6' ),
	'7' => $this->translate( 'mshop/code', 'stat:7' ),
];

$paymentStatusList = [
	'-1' => $this->translate( 'mshop/code', 'pay:-1' ),
	'0' => $this->translate( 'mshop/code', 'pay:0' ),
	'1' => $this->translate( 'mshop/code', 'pay:1' ),
	'2' => $this->translate( 'mshop/code', 'pay:2' ),
	'3' => $this->translate( 'mshop/code', 'pay:3' ),
	'4' => $this->translate( 'mshop/code', 'pay:4' ),
	'5' => $this->translate( 'mshop/code', 'pay:5' ),
	'6' => $this->translate( 'mshop/code', 'pay:6' ),
	'7' => $this->translate( 'mshop/code', 'pay:7' ),
];


?>
<?php $this->block()->start( 'jqadm_content' ) ?>

<?php if( isset( $this->item ) ) : ?>
	<?php $currency = $this->translate( 'currency', $this->item->getPrice()->getCurrencyId() ) ?>

	<form class="item item-order form-horizontal container-fluid" method="POST" enctype="multipart/form-data" action="<?= $enc->attr( $this->link( 'admin/jqadm/url/save', $params ) ) ?>">
		<input id="item-id" type="hidden" name="<?= $enc->attr( $this->formparam( array( 'item', 'order.id' ) ) ) ?>" value="<?= $enc->attr( $this->item->getId() ) ?>">
		<input id="item-next" type="hidden" name="<?= $enc->attr( $this->formparam( array( 'next' ) ) ) ?>" value="get">
		<?= $this->csrf()->formfield() ?>

		<nav class="main-navbar">
			<h1 class="navbar-brand">
				<span class="navbar-title"><?= $enc->html( $this->translate( 'admin', 'Order' ) ) ?></span>
				<span class="navbar-id"><?= $enc->html( $this->item->getId() ) ?></span>
				<span class="navbar-label"><?= $enc->html( $this->number( $this->item->getPrice()->getValue() ) ) ?> <?= $enc->html( $this->item->getPrice()->getCurrencyId() ) ?></span>
				<span class="navbar-site"><?= $enc->html( $this->site()->match( $this->item->locale()->getSiteId() ) ) ?></span>
			</h1>
			<div class="item-actions">
				<a class="btn btn-secondary act-cancel"
					title="<?= $enc->attr( $this->translate( 'admin', 'Cancel and return to list' ) ) ?>"
					href="<?= $enc->attr( $this->link( 'admin/jqadm/url/search', $searchParams ) ) ?>">
					<?= $enc->html( $this->translate( 'admin', 'Cancel' ) ) ?>
				</a>

				<div class="btn-group">
					<button type="submit" class="btn btn-primary act-save"
						title="<?= $enc->attr( $this->translate( 'admin', 'Save entry (Ctrl+S)' ) ) ?>">
						<?= $enc->html( $this->translate( 'admin', 'Save' ) ) ?>
					</button>
					<button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"
						aria-haspopup="true" aria-expanded="false">
						<span class="sr-only"><?= $enc->html( $this->translate( 'admin', 'Toggle dropdown' ) ) ?></span>
					</button>
					<ul class="dropdown-menu dropdown-menu-end">
						<li class="dropdown-item"><a class="next-action" href="#" data-next="search"><?= $enc->html( $this->translate( 'admin', 'Save & Close' ) ) ?></a></li>
					</ul>
				</div>
			</div>
		</nav>

		<div class="row item-container">

			<div class="col-xl-3 item-navbar">
				<div class="navbar-content">
					<ul class="nav nav-tabs flex-xl-column flex-wrap d-flex box" role="tablist">

						<li class="nav-item order">
							<a class="nav-link active" href="#order" data-bs-toggle="tab" role="tab" aria-expanded="true" aria-controls="order">
								<?= $enc->html( $this->translate( 'admin', 'Order' ) ) ?>
							</a>
						</li>

						<?php foreach( array_values( $this->get( 'itemSubparts', [] ) ) as $type => $subpart ) : ?>
							<li class="nav-item <?= $enc->attr( $subpart ) ?>">
								<a class="nav-link" href="#<?= $enc->attr( $subpart ) ?>" data-bs-toggle="tab" role="tab" tabindex="<?= ++$type + 1 ?>">
									<?= $enc->html( $this->translate( 'admin', $subpart ) ) ?>
								</a>
							</li>
						<?php endforeach ?>

					</ul>

					<div class="item-meta text-muted">
						<small>
							<?= $enc->html( $this->translate( 'admin', 'Modified' ) ) ?>:
							<span class="meta-value"><?= $enc->html( $this->item->getTimeModified() ) ?></span>
						</small>
						<small>
							<?= $enc->html( $this->translate( 'admin', 'Created' ) ) ?>:
							<span class="meta-value"><?= $enc->html( $this->item->getTimeCreated() ) ?></span>
						</small>
						<small>
							<?= $enc->html( $this->translate( 'admin', 'Editor' ) ) ?>:
							<span class="meta-value"><?= $enc->html( $this->item->editor() ) ?></span>
						</small>
					</div>

					<div class="more"></div>
				</div>
			</div>

			<div class="col-xl-9 item-content tab-content">

				<div id="order" class="item-order tab-pane fade show active vue" role="tabpanel" aria-labelledby="order">

					<div class="row item-base">
						<div class="col-xl-6 block <?= $this->site()->readonly( $this->item->getSiteId() ) ?>">
							<div class="box">
								<div class="form-group row">
									<label class="col-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Site' ) ) ?></label>
									<div class="col-8">
										<span class="form-control item-sitecode"><?= $enc->html( $this->item->getSiteCode() ) ?></span>
									</div>
									<div class="col-12 form-text text-muted help-text">
										<?= $enc->html( $this->translate( 'admin', 'Site the order was placed at' ) ) ?>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Channel' ) ) ?></label>
									<div class="col-8">
										<span class="form-control item-sitecode"><?= $enc->html( $this->item->getChannel() ) ?></span>
									</div>
									<div class="col-12 form-text text-muted help-text">
										<?= $enc->html( $this->translate( 'admin', 'Channel the order has been placed' ) ) ?>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-4 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Payment' ) ) ?></label>
									<div class="col-8">
										<select class="form-select item-statuspayment" required="required" tabindex="1"
											name="<?= $enc->attr( $this->formparam( array( 'item', 'order.statuspayment' ) ) ) ?>"
											<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?> >
											<?php foreach( $paymentStatusList as $status => $name ) : ?>
												<option value="<?= $enc->attr( $status ) ?>" <?= $selected( $this->item->getStatusPayment(), $status ) ?> >
													<?= $enc->html( $name ) ?>
												</option>
											<?php endforeach ?>
										</select>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-4 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Payment date' ) ) ?></label>
									<div class="col-8">
										<input is="flat-pickr" class="form-control item-datepayment select" type="datetime-local" tabindex="1"
											name="<?= $enc->attr( $this->formparam( array( 'item', 'order.datepayment' ) ) ) ?>"
											placeholder="<?= $enc->attr( $this->translate( 'admin', 'YYYY-MM-DD hh:mm:ss (optional)' ) ) ?>"
											v-bind:value="`<?= $enc->js( $this->datetime( $this->get( 'itemData/order.datepayment' ) ) ) ?>`"
											v-bind:disabled="`<?= $enc->js( $this->site()->readonly( $this->get( 'itemData/order.siteid' ) ) ) ?>` !== ''"
											v-bind:config="Aimeos.flatpickr.datetime">
									</div>
								</div>
								<div class="form-group row">
									<label class="col-4 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Delivery' ) ) ?></label>
									<div class="col-8">
										<select class="form-select item-statusdelivery" required="required" tabindex="1"
											name="<?= $enc->attr( $this->formparam( array( 'item', 'order.statusdelivery' ) ) ) ?>"
											<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?> >
											<?php foreach( $deliveryStatusList as $status => $name ) : ?>
												<option value="<?= $enc->attr( $status ) ?>" <?= $selected( $this->item->getStatusDelivery(), $status ) ?> >
													<?= $enc->html( $name ) ?>
												</option>
											<?php endforeach ?>
										</select>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-4 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Delivery date' ) ) ?></label>
									<div class="col-8">
										<input is="flat-pickr" class="form-control item-datedelivery select" type="datetime-local" tabindex="1"
											name="<?= $enc->attr( $this->formparam( array( 'item', 'order.datedelivery' ) ) ) ?>"
											placeholder="<?= $enc->attr( $this->translate( 'admin', 'YYYY-MM-DD hh:mm:ss (optional)' ) ) ?>"
											v-bind:value="`<?= $enc->js( $this->datetime( $this->get( 'itemData/order.datedelivery' ) ) ) ?>`"
											v-bind:disabled="`<?= $enc->js( $this->site()->readonly( $this->get( 'itemData/order.siteid' ) ) ) ?>` !== ''"
											v-bind:config="Aimeos.flatpickr.datetime">
									</div>
								</div>
							</div>
						</div>

						<div class="col-xl-6 block <?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>">
							<div class="box">
								<div class="form-group row">
									<label class="col-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Invoice No.' ) ) ?></label>
									<div class="col-8">
										<span class="form-control item-sitecode"><?= $enc->html( $this->item->getInvoiceNumber() ) ?></span>
									</div>
									<div class="col-12 form-text text-muted help-text">
										<?= $enc->html( $this->translate( 'admin', 'Invoice number for the order' ) ) ?>
									</div>
								</div>
								<?php if( strncmp( $this->site()->siteid(), $this->item->getSiteId(), strlen( $this->site()->siteid() ) ) === 0 ) : ?>
									<div class="form-group row">
										<label class="col-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Related ID' ) ) ?></label>
										<div class="col-8">
											<input class="form-control item-relatedid" type="text" tabindex="1"
												name="<?= $enc->attr( $this->formparam( array( 'item', 'order.relatedid' ) ) ) ?>"
												placeholder="<?= $enc->attr( $this->translate( 'admin', 'Related order ID (optional)' ) ) ?>"
												value="<?= $enc->attr( $this->get( 'itemData/order.relatedid' ) ) ?>"
												<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>>
										</div>
										<div class="col-12 form-text text-muted help-text">
											<?= $enc->html( $this->translate( 'admin', 'Order number entered by the customer' ) ) ?>
										</div>
									</div>
								<?php endif ?>
								<div class="form-group row">
									<label class="col-4 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Customer ID' ) ) ?></label>
									<div class="col-8">
										<span class="form-control item-customerid">
											<?php if( $this->item->getCustomerId() && $this->access( $this->config( 'admin/jqadm/resource/customer/groups', [] ) ) ) : ?>
												<a class="link fa act-view" target="_blank"
													href="<?= $enc->attr( $this->link( 'admin/jqadm/url/get', ['resource' => 'customer', 'id' => $this->item->getCustomerId(), 'locale' => $this->param( 'locale' )] ) ) ?>">
													<?= $enc->html( $this->item->getCustomerId() ) ?>
												</a>
											<?php endif ?>
										</span>
									</div>
								</div>
								<?php if( $this->access( $this->config( 'admin/jqadm/resource/customer/groups', [] ) ) ) : ?>
									<div class="form-group row">
										<label class="col-4 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Customer' ) ) ?></label>
										<div class="col-8">
											<select class="form-select combobox item-customer" tabindex="1"
												name="<?= $enc->attr( $this->formparam( array( 'item', 'order.customerid' ) ) ) ?>"
												<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>>
												<option value="<?= $enc->attr( $this->get( 'itemData/order.customerid' ) ) ?>" >
													<?= $enc->html( $this->get( 'itemData/customer.code' ) ) ?>
												</option>
											</select>
										</div>
									</div>
								<?php endif ?>
								<div class="form-group row">
									<label class="col-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Customer reference' ) ) ?></label>
									<div class="col-8">
										<input class="form-control item-customerref" type="text" tabindex="1"
											name="<?= $enc->attr( $this->formparam( array( 'item', 'order.customerref' ) ) ) ?>"
											placeholder="<?= $enc->attr( $this->translate( 'admin', 'Customer reference (optional)' ) ) ?>"
											value="<?= $enc->attr( $this->get( 'itemData/order.customerref' ) ) ?>"
											<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>>
									</div>
									<div class="col-12 form-text text-muted help-text">
										<?= $enc->html( $this->translate( 'admin', 'Order number entered by the customer' ) ) ?>
									</div>
								</div>
								<div class="form-group row mandatory">
									<label class="col-4 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Language' ) ) ?></label>
									<div class="col-8">
										<select class="form-select item-languageid" required="required" tabindex="1"
											name="<?= $enc->attr( $this->formparam( array( 'item', 'order.languageid' ) ) ) ?>"
											<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?> >
											<option value="">
												<?= $enc->html( $this->translate( 'admin', 'Please select' ) ) ?>
											</option>

											<?php foreach( $this->get( 'pageLangItems', [] ) as $langId => $langItem ) : ?>
												<option value="<?= $enc->attr( $langId ) ?>" <?= $selected( $this->item->locale()->getLanguageId(), $langId ) ?> >
													<?= $enc->html( $this->translate( 'language', $langId ) ) ?>
												</option>
											<?php endforeach ?>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>


					<div class="box">
						<div class="row item-product-list">

							<div class="col-sm-12">
								<h2 class="item-header"><?= $enc->html( $this->translate( 'admin', 'Products' ) ) ?></h2>

								<?php foreach( $this->item->getProducts() as $pos => $orderProduct ) : ?>
									<?php if( strncmp( $this->site()->siteid(), $orderProduct->getSiteId(), strlen( $this->site()->siteid() ) ) === 0 ) : ?>

										<div class="list-item">
											<div class="row">
												<div class="col-sm-2 item-column column-subscription">
													<div class="row">
														<label class="col-5 col-sm-12 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Renew' ) ) ?></label>
														<div class="col-7 col-sm-12">
															<?php $newParams = [
																	'item' => ['subscription.orderid' => $this->item->getId(), 'subscription.ordprodid' => $orderProduct->getId()],
																	'site' => $this->param( 'site' ),
																	'locale' => $this->param( 'locale' ),
																	'resource' => 'subscription'
																];
															?>
															<a class="btn btn-subscription fa"
																href="<?= $this->link( 'admin/jqadm/url/create', $newParams ) ?>"
																title="<?= $enc->html( $this->translate( 'admin', 'Renew' ) ) ?>"></a>
														</div>
													</div>
												</div>
												<div class="col-sm-4 item-column column-desc">
													<div class="row">
														<label class="col-5 col-sm-12 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Name' ) ) ?></label>
														<div class="col-7 col-sm-12">
															<span class="product-name"><?= $enc->html( $orderProduct->getName() ) ?></span>
															<span class="product-attr">
																<?php foreach( $orderProduct->getAttributeItems() as $attrItem ) : ?>
																	<span class="attr-code"><?= $enc->html( $attrItem->getCode() ) ?></span>
																	<span class="attr-value">
																		<?php if( $attrItem->getQuantity() > 1 ) : ?>
																			<?= $enc->html( $attrItem->getQuantity() ) ?>×
																		<?php endif ?>
																		<?= $enc->html( join( ',', (array) $attrItem->getValue() ) ) ?>
																	</span>
																<?php endforeach ?>
															</span>
															<span class="product-sku"><?= $enc->html( $orderProduct->getProductCode() ) ?></span>
														</div>
													</div>
												</div>
												<div class="col-sm-2 item-column column-sum">
													<div class="row">
														<label class="col-5 col-sm-12 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Sum' ) ) ?></label>
														<div class="col-7 col-sm-12">
															<span class="product-price"><?= $enc->html( sprintf( $priceFormat, $this->number( $orderProduct->getPrice()->getValue() * $orderProduct->getQuantity() ), $currency ) ) ?></span>
															<span class="product-rebate"><?= $enc->html( sprintf( $priceFormat, $this->number( $orderProduct->getPrice()->getRebate() * $orderProduct->getQuantity() ), $currency ) ) ?></span>
														</div>
													</div>
												</div>
												<div class="col-sm-4 item-column column-qtyopen">
													<div class="row">
														<label class="col-5 col-sm-12 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Open Qty' ) ) ?></label>
														<div class="col-7 col-sm-12">
															<input class="form-control product-qtyopen" type="number" tabindex="1" step="0.001"
																name="<?= $enc->attr( $this->formparam( array( 'item', 'product', $pos, 'order.product.qtyopen' ) ) ) ?>"
																placeholder="<?= $enc->attr( $this->translate( 'admin', 'Open' ) ) ?>"
																value="<?= $enc->attr( $this->get( 'itemData/product/' . $pos . '/order.product.qtyopen' ) ) ?>"
																<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>
															> / <span class="product-quantity"><?= $enc->html( $orderProduct->getQuantity() ) ?></span>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-4 item-column column-statuspayment">
													<div class="row">
														<label class="col-5 col-sm-12 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Payment' ) ) ?></label>
														<div class="col-7 col-sm-12">
															<select class="form-select product-status" tabindex="1"
																name="<?= $enc->attr( $this->formparam( array( 'item', 'product', $pos, 'order.product.statuspayment' ) ) ) ?>"
																<?= $this->site()->readonly( $orderProduct->getSiteId() ) ?> >
																<option value=""></option>
																<?php foreach( $paymentStatusList as $code => $label ) : ?>
																	<option value="<?= $code ?>" <?= $selected( $this->get( 'itemData/product/' . $pos . '/order.product.statuspayment' ), $code ) ?> >
																		<?= $enc->html( $label ) ?>
																	</option>
																<?php endforeach ?>
															</select>
														</div>
													</div>
												</div>
												<div class="col-sm-4 item-column column-statusdelivery">
													<div class="row">
														<label class="col-5 col-sm-12 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Delivery' ) ) ?></label>
														<div class="col-7 col-sm-12">
															<select class="form-select product-status" tabindex="1"
																name="<?= $enc->attr( $this->formparam( array( 'item', 'product', $pos, 'order.product.statusdelivery' ) ) ) ?>"
																<?= $this->site()->readonly( $orderProduct->getSiteId() ) ?> >
																<option value=""></option>
																<?php foreach( $deliveryStatusList as $code => $label ) : ?>
																	<option value="<?= $code ?>" <?= $selected( $this->get( 'itemData/product/' . $pos . '/order.product.statusdelivery' ), $code ) ?> >
																		<?= $enc->html( $label ) ?>
																	</option>
																<?php endforeach ?>
															</select>
														</div>
													</div>
												</div>
												<div class="col-sm-4 item-column column-timeframe">
													<div class="row">
														<label class="col-5 col-sm-12 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Delivery in' ) ) ?></label>
														<div class="col-7 col-sm-12">
															<input class="form-control product-timeframe" maxlength="16" tabindex="1"
																name="<?= $enc->attr( $this->formparam( array( 'item', 'product', $pos, 'order.product.timeframe' ) ) ) ?>"
																placeholder="<?= $enc->attr( $this->translate( 'admin', 'Delivery timeframe (optional)' ) ) ?>"
																value="<?= $enc->attr( $this->get( 'itemData/product/' . $pos . '/order.product.timeframe' ) ) ?>"
																<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-12 item-column column-notes">
													<div class="row">
														<label class="col-5 col-sm-12 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Notes' ) ) ?></label>
														<div class="col-7 col-sm-12">
															<input class="form-control product-notes" tabindex="1" maxlength="255"
																name="<?= $enc->attr( $this->formparam( array( 'item', 'product', $pos, 'order.product.notes' ) ) ) ?>"
																placeholder="<?= $enc->attr( $this->translate( 'admin', 'Notes (not shown to customer)' ) ) ?>"
																value="<?= $enc->attr( $this->get( 'itemData/product/' . $pos . '/order.product.notes' ) ) ?>"
																<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>>
														</div>
													</div>
												</div>
											</div>
										</div>

									<?php endif ?>
								<?php endforeach ?>

							</div>

						</div>
					</div>


					<div class="row item-misc">
						<div class="col-xl-6">
							<div class="box">
								<h2 class="col-sm-12 item-header"><?= $enc->html( $this->translate( 'admin', 'Coupon' ) ) ?></h2>
								<?php if( !$this->item->getCoupons()->isEmpty() ) : ?>
									<?php foreach( $this->item->getCoupons() as $code => $product ) : ?>
										<div class="form-group row">
											<label class="col-sm-4"><?= $enc->html( $this->translate( 'admin', 'Code' ) ) ?></label>
											<div class="col-sm-8"><span class="item-coupon"><?= $enc->attr( $code ) ?></span></div>
										</div>
									<?php endforeach ?>
								<?php else : ?>
									<?= $enc->html( $this->translate( 'admin', 'No voucher' ) ) ?>
								<?php endif ?>
							</div>
						</div>

						<div class="col-xl-6">
							<div class="box">
								<h2 class="col-sm-12 item-header"><?= $enc->html( $this->translate( 'admin', 'Comment' ) ) ?></h2>
								<div class="form-group optional">
									<textarea class="form-control item-title" type="text" tabindex="1" rows="3"
										name="<?= $enc->attr( $this->formparam( array( 'item', 'order.comment' ) ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'admin', 'Customer comment (optional)' ) ) ?>"
										<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>
									><?= $enc->html( $this->item->getComment() ) ?></textarea>
								</div>
							</div>
						</div>
					</div>


					<div class="row">
						<?php foreach( $this->item->getAddresses()->krsort() as $type => $addresses ) : $code = 'address:' . $type ?>

							<div class="col-xl-6 item-address">
								<div class="box">
									<h2 class="col-sm-12 item-header"><?= $enc->html( $this->translate( 'admin/ext', $code ) ) ?></h2>

									<?php foreach( $addresses as $pos => $addrItem ) : ?>
										<div class="address-short">
											<?php
												/// short order address with company (%1$s), first name (%2$s), last name (%3$s), street (%4$s), house number (%5$s),
												/// zip code (%6$s), city (%7$s),state (%8$s), countryid (%9$s), e-mail (%10$s), telephone (%11$s), VAT ID  (%12$s)
												$addrFormat = $this->translate( 'admin', "%1\$s\n%2\$s %3\$s\n%5\$s %4\$s\n%7\$s, %6\$s\n%8\$s, %9\$s\n%10\$s\n%11\$s\n%12\$s" );
											?>
											<span class="address-text" data-format="<?= $enc->attr( $addrFormat ) ?>"><!-- inserted by order.js --></span>
											<span class="address-edit"></span>
										</div>

										<fieldset class="address-form">
											<div class="form-group row mandatory">
												<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'E-Mail' ) ) ?></label>
												<div class="col-sm-8">
													<input class="form-control item-email" type="email" tabindex="1" data-field="email"
														name="<?= $enc->attr( $this->formparam( array( 'item', 'address', $type, $pos, 'order.address.email' ) ) ) ?>"
														placeholder="<?= $enc->attr( $this->translate( 'admin', 'E-Mail address (required)' ) ) ?>"
														value="<?= $enc->attr( $this->get( 'itemData/address/' . $type . '/' . $pos . '/order.address.email' ) ) ?>"
														<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>>
												</div>
												<div class="col-sm-12 form-text text-muted help-text">
													<?= $enc->html( $this->translate( 'admin', 'Customer e-mail address' ) ) ?>
												</div>
											</div>
											<div class="form-group row optional">
												<label class="col-sm-4 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Language' ) ) ?></label>
												<div class="col-sm-8">
													<select class="form-select item-languageid" tabindex="1" data-field="languageid"
														name="<?= $enc->attr( $this->formparam( array( 'item', 'address', $type, $pos, 'order.address.languageid' ) ) ) ?>"
														<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?> >
														<option value=""><?= $enc->html( $this->translate( 'admin', 'Please select' ) ) ?></option>

														<?php foreach( $this->get( 'pageLangItems', [] ) as $langId => $langItem ) : ?>
															<option value="<?= $enc->attr( $langId ) ?>" <?= $selected( $this->get( 'itemData/address/' . $type . '/' . $pos . '/order.address.languageid' ), $langId ) ?> >
																<?= $enc->html( $this->translate( 'language', $langId ) ) ?>
															</option>
														<?php endforeach ?>
													</select>
												</div>
											</div>
											<div class="form-group row optional">
												<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Salutation' ) ) ?></label>
												<div class="col-sm-8">
													<select class="form-select item-salutation" tabindex="1" data-field="salutation"
														name="<?= $enc->attr( $this->formparam( array( 'item', 'address', $type, $pos, 'order.address.salutation' ) ) ) ?>"
														<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?> >
														<option value="" <?= $selected( $this->get( 'itemData/address/' . $type . '/' . $pos . '/order.address.salutation' ), '' ) ?> >
															<?= $enc->html( $this->translate( 'admin', 'none' ) ) ?>
														</option>
														<option value="company" <?= $selected( $this->get( 'itemData/address/' . $type . '/' . $pos . '/order.address.salutation' ), 'company' ) ?> >
															<?= $enc->html( $this->translate( 'client/code', 'company' ) ) ?>
														</option>
														<option value="mr" <?= $selected( $this->get( 'itemData/address/' . $type . '/' . $pos . '/order.address.salutation' ), 'mr' ) ?> >
															<?= $enc->html( $this->translate( 'client/code', 'mr' ) ) ?>
														</option>
														<option value="ms" <?= $selected( $this->get( 'itemData/address/' . $type . '/' . $pos . '/order.address.salutation' ), 'ms' ) ?> >
															<?= $enc->html( $this->translate( 'client/code', 'ms' ) ) ?>
														</option>
													</select>
												</div>
												<div class="col-sm-12 form-text text-muted help-text">
													<?= $enc->html( $this->translate( 'admin', 'How the customer is addressed in e-mails' ) ) ?>
												</div>
											</div>
											<div class="form-group row optional">
												<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Title' ) ) ?></label>
												<div class="col-sm-8">
													<input class="form-control item-title" type="text" tabindex="1" data-field="title"
														name="<?= $enc->attr( $this->formparam( array( 'item', 'address', $type, $pos, 'order.address.title' ) ) ) ?>"
														placeholder="<?= $enc->attr( $this->translate( 'admin', 'Honorary title (optional)' ) ) ?>"
														value="<?= $enc->attr( $this->get( 'itemData/address/' . $type . '/' . $pos . '/order.address.title' ) ) ?>"
														<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>>
												</div>
												<div class="col-sm-12 form-text text-muted help-text">
													<?= $enc->html( $this->translate( 'admin', 'Honorary titles like Dr., Ph.D, etc.' ) ) ?>
												</div>
											</div>
											<div class="form-group row mandatory">
												<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Last name' ) ) ?></label>
												<div class="col-sm-8">
													<input class="form-control item-lastname" type="text" required="required" tabindex="1" data-field="lastname"
														name="<?= $enc->attr( $this->formparam( array( 'item', 'address', $type, $pos, 'order.address.lastname' ) ) ) ?>"
														placeholder="<?= $enc->attr( $this->translate( 'admin', 'Last name (required)' ) ) ?>"
														value="<?= $enc->attr( $this->get( 'itemData/address/' . $type . '/' . $pos . '/order.address.lastname' ) ) ?>"
														<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>>
												</div>
												<div class="col-sm-12 form-text text-muted help-text">
													<?= $enc->html( $this->translate( 'admin', 'Last name of the person or full name in cultures where no first names are used' ) ) ?>
												</div>
											</div>
											<div class="form-group row optional">
												<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'First name' ) ) ?></label>
												<div class="col-sm-8">
													<input class="form-control item-firstname" type="text" tabindex="1" data-field="firstname"
														name="<?= $enc->attr( $this->formparam( array( 'item', 'address', $type, $pos, 'order.address.firstname' ) ) ) ?>"
														placeholder="<?= $enc->attr( $this->translate( 'admin', 'First name (optional)' ) ) ?>"
														value="<?= $enc->attr( $this->get( 'itemData/address/' . $type . '/' . $pos . '/order.address.firstname' ) ) ?>"
														<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>>
												</div>
												<div class="col-sm-12 form-text text-muted help-text">
													<?= $enc->html( $this->translate( 'admin', 'First name of the person if used in cultures where they are used' ) ) ?>
												</div>
											</div>
											<div class="form-group row optional">
												<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Street' ) ) ?></label>
												<div class="col-sm-8">
													<input class="form-control item-address1" type="text" tabindex="1" data-field="address1"
														name="<?= $enc->attr( $this->formparam( array( 'item', 'address', $type, $pos, 'order.address.address1' ) ) ) ?>"
														placeholder="<?= $enc->attr( $this->translate( 'admin', 'Street name (optional)' ) ) ?>"
														value="<?= $enc->attr( $this->get( 'itemData/address/' . $type . '/' . $pos . '/order.address.address1' ) ) ?>"
														<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>>
												</div>
												<div class="col-sm-12 form-text text-muted help-text">
													<?= $enc->html( $this->translate( 'admin', 'First name of the person if used in cultures where they are used' ) ) ?>
												</div>
											</div>
											<div class="form-group row optional">
												<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'House number' ) ) ?></label>
												<div class="col-sm-8">
													<input class="form-control item-address2" type="text" tabindex="1" data-field="address2"
														name="<?= $enc->attr( $this->formparam( array( 'item', 'address', $type, $pos, 'order.address.address2' ) ) ) ?>"
														placeholder="<?= $enc->attr( $this->translate( 'admin', 'House number (optional)' ) ) ?>"
														value="<?= $enc->attr( $this->get( 'itemData/address/' . $type . '/' . $pos . '/order.address.address2' ) ) ?>"
														<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>>
												</div>
												<div class="col-sm-12 form-text text-muted help-text">
													<?= $enc->html( $this->translate( 'admin', 'Address identifier of the customer\'s house for delivery' ) ) ?>
												</div>
											</div>
											<div class="form-group row optional">
												<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Floor / Appartment' ) ) ?></label>
												<div class="col-sm-8">
													<input class="form-control item-address3" type="text" tabindex="1" data-field="address3"
														name="<?= $enc->attr( $this->formparam( array( 'item', 'address', $type, $pos, 'order.address.address3' ) ) ) ?>"
														placeholder="<?= $enc->attr( $this->translate( 'admin', 'Floor and/or apartment (optional)' ) ) ?>"
														value="<?= $enc->attr( $this->get( 'itemData/address/' . $type . '/' . $pos . '/order.address.address3' ) ) ?>"
														<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>>
												</div>
												<div class="col-sm-12 form-text text-muted help-text">
													<?= $enc->html( $this->translate( 'admin', 'Additional information where the customer\'s apartment can be found' ) ) ?>
												</div>
											</div>
											<div class="form-group row optional">
												<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Zip code' ) ) ?></label>
												<div class="col-sm-8">
													<input class="form-control item-postal" type="text" tabindex="1" data-field="postal"
														name="<?= $enc->attr( $this->formparam( array( 'item', 'address', $type, $pos, 'order.address.postal' ) ) ) ?>"
														placeholder="<?= $enc->attr( $this->translate( 'admin', 'Zip code (optional)' ) ) ?>"
														value="<?= $enc->attr( $this->get( 'itemData/address/' . $type . '/' . $pos . '/order.address.postal' ) ) ?>"
														<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>>
												</div>
												<div class="col-sm-12 form-text text-muted help-text">
													<?= $enc->html( $this->translate( 'admin', 'Postal code for delivery if used in the area the customer is living' ) ) ?>
												</div>
											</div>
											<div class="form-group row mandatory">
												<label class="col-sm-4 form-control-label"><?= $enc->html( $this->translate( 'admin', 'City' ) ) ?></label>
												<div class="col-sm-8">
													<input class="form-control item-city" type="text" required="required" tabindex="1" data-field="city"
														name="<?= $enc->attr( $this->formparam( array( 'item', 'address', $type, $pos, 'order.address.city' ) ) ) ?>"
														placeholder="<?= $enc->attr( $this->translate( 'admin', 'City or town name (required)' ) ) ?>"
														value="<?= $enc->attr( $this->get( 'itemData/address/' . $type . '/' . $pos . '/order.address.city' ) ) ?>"
														<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>>
												</div>
											</div>
											<div class="form-group row optional">
												<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Country' ) ) ?></label>
												<div class="col-sm-8">
													<select class="form-select item-countryid" required="required" tabindex="1"
														name="<?= $enc->attr( $this->formparam( array( 'item', 'address', $type, $pos, 'order.address.countryid' ) ) ) ?>"
														<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>>
														<option value="" disabled>
															<?= $enc->html( $this->translate( 'admin', 'Please select' ) ) ?>
														</option>

														<?php foreach( $this->get( 'countries', [] ) as $code => $label ) : ?>
															<option value="<?= $enc->attr( $code ) ?>" <?= $selected( $this->get( 'itemData/address/' . $type . '/' . $pos . '/order.address.countryid' ), $code ) ?> >
																<?= $enc->html( $label ) ?>
															</option>
														<?php endforeach ?>
													</select>
												</div>
												<div class="col-sm-12 form-text text-muted help-text">
													<?= $enc->html( $this->translate( 'admin', 'Two letter ISO country code' ) ) ?>
												</div>
											</div>
											<div class="form-group row optional">
												<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'State' ) ) ?></label>
												<div class="col-sm-8">
													<input class="form-control item-state" type="text" tabindex="1" data-field="state"
														name="<?= $enc->attr( $this->formparam( array( 'item', 'address', $type, $pos, 'order.address.state' ) ) ) ?>"
														placeholder="<?= $enc->attr( $this->translate( 'admin', 'Country state code (optional)' ) ) ?>"
														value="<?= $enc->attr( $this->get( 'itemData/address/' . $type . '/' . $pos . '/order.address.state' ) ) ?>"
														<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>>
												</div>
												<div class="col-sm-12 form-text text-muted help-text">
													<?= $enc->html( $this->translate( 'admin', 'Short state code (e.g. NY) if used in the country the customer is living' ) ) ?>
												</div>
											</div>
											<div class="form-group row optional">
												<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Telephone' ) ) ?></label>
												<div class="col-sm-8">
													<input class="form-control item-telephone" type="tel" tabindex="1" data-field="telephone"
														name="<?= $enc->attr( $this->formparam( array( 'item', 'address', $type, $pos, 'order.address.telephone' ) ) ) ?>"
														placeholder="<?= $enc->attr( $this->translate( 'admin', 'Telephone number (optional)' ) ) ?>"
														value="<?= $enc->attr( $this->get( 'itemData/address/' . $type . '/' . $pos . '/order.address.telephone' ) ) ?>"
														<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>>
												</div>
												<div class="col-sm-12 form-text text-muted help-text">
													<?= $enc->html( $this->translate( 'admin', '(International) telephone number without separation characters, can start with a "+"' ) ) ?>
												</div>
											</div>
											<div class="form-group row optional">
												<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Facsimile' ) ) ?></label>
												<div class="col-sm-8">
													<input class="form-control item-telefax" type="text" tabindex="1" data-field="telefax"
														name="<?= $enc->attr( $this->formparam( array( 'item', 'address', $type, $pos, 'order.address.telefax' ) ) ) ?>"
														placeholder="<?= $enc->attr( $this->translate( 'admin', 'Facsimile number (optional)' ) ) ?>"
														value="<?= $enc->attr( $this->get( 'itemData/address/' . $type . '/' . $pos . '/order.address.telefax' ) ) ?>"
														<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>>
												</div>
												<div class="col-sm-12 form-text text-muted help-text">
													<?= $enc->html( $this->translate( 'admin', '(International) facsimilie number without separation characters, can start with a "+"' ) ) ?>
												</div>
											</div>
											<div class="form-group row optional">
												<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Web site' ) ) ?></label>
												<div class="col-sm-8">
													<input class="form-control item-website" type="url" tabindex="1" data-field="website"
														name="<?= $enc->attr( $this->formparam( array( 'item', 'address', $type, $pos, 'order.address.website' ) ) ) ?>"
														placeholder="<?= $enc->attr( $this->translate( 'admin', 'Web site URL (optional)' ) ) ?>"
														value="<?= $enc->attr( $this->get( 'itemData/address/' . $type . '/' . $pos . '/order.address.website' ) ) ?>"
														<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>>
												</div>
												<div class="col-sm-12 form-text text-muted help-text">
													<?= $enc->html( $this->translate( 'admin', 'URL of the customer web site' ) ) ?>
												</div>
											</div>
											<div class="form-group row optional">
												<label class="col-sm-4 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Company' ) ) ?></label>
												<div class="col-sm-8">
													<input class="form-control item-company" type="text" tabindex="1" data-field="company"
														name="<?= $enc->attr( $this->formparam( array( 'item', 'address', $type, $pos, 'order.address.company' ) ) ) ?>"
														placeholder="<?= $enc->attr( $this->translate( 'admin', 'Company name (optional)' ) ) ?>"
														value="<?= $enc->attr( $this->get( 'itemData/address/' . $type . '/' . $pos . '/order.address.company' ) ) ?>"
														<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>>
												</div>
											</div>
											<div class="form-group row optional">
												<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'VAT ID' ) ) ?></label>
												<div class="col-sm-8">
													<input class="form-control item-vatid" type="text" tabindex="1" data-field="vatid"
														name="<?= $enc->attr( $this->formparam( array( 'item', 'address', $type, $pos, 'order.address.vatid' ) ) ) ?>"
														placeholder="<?= $enc->attr( $this->translate( 'admin', 'Value added tax identifier (optional)' ) ) ?>"
														value="<?= $enc->attr( $this->get( 'itemData/address/' . $type . '/' . $pos . '/order.address.vatid' ) ) ?>"
														<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>>
												</div>
												<div class="col-sm-12 form-text text-muted help-text">
													<?= $enc->html( $this->translate( 'admin', 'Official VAT ID to determine if the tax needs to be billed in invoices' ) ) ?>
												</div>
											</div>
										</fieldset>

									<?php endforeach ?>
								</div>
							</div>

						<?php endforeach ?>
					</div>

					<div class="row">
						<?php foreach( $this->item->getServices()->krsort() as $type => $services ) : $code = 'service:' . $type ?>
							<?php foreach( $services as $serviceItem ) : $serviceId = $serviceItem->getServiceId() ?>

								<div class="col-xl-6 item-service">
									<div class="box">
										<h2 class="col-12 item-header"><?= $enc->html( $this->translate( 'admin/ext', $code ) ) ?></h2>
										<div class="row">
											<div class="col-6">
												<span class="service-name"><?= $enc->html( $serviceItem->getName() ) ?></span>
												<span class="service-code"><?= $enc->html( $serviceItem->getCode() ) ?></span>
											</div>
											<div class="col-6">
												<span class="service-price"><?= $enc->html( sprintf( $priceFormat, $this->number( $serviceItem->getPrice()->getValue() + $serviceItem->getPrice()->getCosts() ), $currency ) ) ?></span>
												<?php if( $serviceItem->getPrice()->getRebate() > 0 ) : ?>
													<span class="service-rebate"><?= $enc->html( sprintf( $priceFormat, $this->number( $serviceItem->getPrice()->getRebate() ), $currency ) ) ?></span>
												<?php endif ?>
											</div>
										</div>

										<table class="service-attr table" data-id="<?= $enc->attr( $serviceId ) ?>"
											data-codes="<?= $enc->attr( isset( $serviceAttrCodes[$type] ) ? implode( ',', $serviceAttrCodes[$type] ) : '' ) ?>">
											<thead>
												<tr>
													<th>
														<span class="help"><?= $enc->html( $this->translate( 'admin', 'Code' ) ) ?></span>
														<div class="form-text text-muted help-text">
															<?= $enc->html( $this->translate( 'admin', 'Service attribute code' ) ) ?>
														</div>
													</th>
													<th>
														<?= $enc->html( $this->translate( 'admin', 'Value' ) ) ?>
													</th>
													<th class="actions">
														<?php if( !$this->site()->readonly( $this->item->locale()->getSiteId() ) ) : ?>
															<div class="btn act-add fa" tabindex="1"
																title="<?= $enc->attr( $this->translate( 'admin', 'Insert new entry (Ctrl+I)' ) ) ?>">
															</div>
														<?php endif ?>
													</th>
												</tr>
											</thead>
											<tbody>

												<?php foreach( (array) $this->get( 'itemData/service/' . $type . '/' . $serviceId . '/order.service.attribute.id', [] ) as $idx => $attrId ) : ?>
													<tr class="service-attr-item">
														<td>
															<input type="hidden" class="service-attr-id" value="<?= $enc->attr( $attrId ) ?>"
																name="<?= $enc->attr( $this->formparam( array( 'item', 'service', $type, $serviceId, 'order.service.attribute.id', '' ) ) ) ?>">
															<input type="hidden" class="service-attr-attributeid"
																name="<?= $enc->attr( $this->formparam( array( 'item', 'service', $type, $serviceId, 'order.service.attribute.attrid', '' ) ) ) ?>"
																value="<?= $enc->attr( $this->get( 'itemData/service/' . $type . '/' . $serviceId . '/order.service.attribute.attrid/' . $idx ) ) ?>">
															<input type="hidden" class="service-attr-type"
																name="<?= $enc->attr( $this->formparam( array( 'item', 'service', $type, $serviceId, 'order.service.attribute.type', '' ) ) ) ?>"
																value="<?= $enc->attr( $this->get( 'itemData/service/' . $type . '/' . $serviceId . '/order.service.attribute.type/' . $idx ) ) ?>">
															<input type="hidden" class="service-attr-name"
																name="<?= $enc->attr( $this->formparam( array( 'item', 'service', $type, $serviceId, 'order.service.attribute.name', '' ) ) ) ?>"
																value="<?= $enc->attr( $this->get( 'itemData/service/' . $type . '/' . $serviceId . '/order.service.attribute.name/' . $idx ) ) ?>">
															<input type="hidden" class="service-attr-quantity"
																name="<?= $enc->attr( $this->formparam( array( 'item', 'service', $type, $serviceId, 'order.service.attribute.quantity', '' ) ) ) ?>"
																value="<?= $enc->attr( $this->get( 'itemData/service/' . $type . '/' . $serviceId . '/order.service.attribute.quantity/' . $idx ) ) ?>">
															<input type="text" class="service-attr-code form-control" tabindex="1"
																name="<?= $enc->attr( $this->formparam( array( 'item', 'service', $type, $serviceId, 'order.service.attribute.code', '' ) ) ) ?>"
																value="<?= $enc->attr( $this->get( 'itemData/service/' . $type . '/' . $serviceId . '/order.service.attribute.code/' . $idx ) ) ?>"
																<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>>
														</td>
														<td>
															<input type="text" class="service-attr-value form-control" tabindex="1"
																name="<?= $enc->attr( $this->formparam( array( 'item', 'service', $type, $serviceId, 'order.service.attribute.value', '' ) ) ) ?>"
																value="<?= $enc->attr( $this->get( 'itemData/service/' . $type . '/' . $serviceId . '/order.service.attribute.value/' . $idx ) ) ?>"
																<?= $this->site()->readonly( $this->item->locale()->getSiteId() ) ?>>
														</td>
														<td class="actions">
															<?php if( !$this->site()->readonly( $this->item->locale()->getSiteId() ) ) : ?>
																<div class="btn act-delete fa" tabindex="1"
																	title="<?= $enc->attr( $this->translate( 'admin', 'Delete this entry' ) ) ?>">
																</div>
															<?php endif ?>
														</td>
													</tr>
												<?php endforeach ?>

												<tr class="prototype">
													<td>
														<input type="hidden" class="service-attr-id" value="" disabled="disabled"
															name="<?= $enc->attr( $this->formparam( array( 'item', 'service', $type, '_id_', 'order.service.attribute.id', '' ) ) ) ?>">
														<input type="hidden" class="service-attr-attributeid" value="" disabled="disabled"
															name="<?= $enc->attr( $this->formparam( array( 'item', 'service', $type, '_id_', 'order.service.attribute.attrid', '' ) ) ) ?>">
														<input type="hidden" class="service-attr-type" value="<?= $enc->attr( $type ) ?>" disabled="disabled"
															name="<?= $enc->attr( $this->formparam( array( 'item', 'service', $type, '_id_', 'order.service.attribute.type', '' ) ) ) ?>">
														<input type="hidden" class="service-attr-name" value="" disabled="disabled"
															name="<?= $enc->attr( $this->formparam( array( 'item', 'service', $type, '_id_', 'order.service.attribute.name', '' ) ) ) ?>">
														<input type="hidden" class="service-attr-quantity" value="1" disabled="disabled"
															name="<?= $enc->attr( $this->formparam( array( 'item', 'service', $type, '_id_', 'order.service.attribute.quantity', '' ) ) ) ?>">
														<input type="text" class="service-attr-code form-control" value="" disabled="disabled" tabindex="1"
															name="<?= $enc->attr( $this->formparam( array( 'item', 'service', $type, '_id_', 'order.service.attribute.code', '' ) ) ) ?>">
													</td>
													<td>
														<input type="text" class="service-attr-value form-control" value="" disabled="disabled" tabindex="1"
															name="<?= $enc->attr( $this->formparam( array( 'item', 'service', $type, '_id_', 'order.service.attribute.value', '' ) ) ) ?>">
													</td>
													<td class="actions">
														<?php if( !$this->site()->readonly( $this->item->locale()->getSiteId() ) ) : ?>
															<div class="btn act-delete fa" tabindex="1"
																title="<?= $enc->attr( $this->translate( 'admin', 'Delete this entry' ) ) ?>">
															</div>
														<?php endif ?>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>

							<?php endforeach ?>
						<?php endforeach ?>
					</div>

					<?php if( $this->site()->siteid() == $this->item->getSiteId() ) : ?>
						<div class="row item-summary justify-content-end">
							<div class="col-xl-6 item-total">
								<div class="box">
									<h2 class="item-header"><?= $enc->html( $this->translate( 'admin', 'Order totals' ) ) ?></h2>
									<div class="form-group row total-subtotal">
										<div class="col-6 name"><?= $enc->html( $this->translate( 'admin', 'Sub-total' ) ) ?></div>
										<div class="col-6 value"><?= $enc->html( sprintf( $priceFormat, $this->number( $this->item->getPrice()->getValue() ), $currency ) ) ?></div>
									</div>
									<div class="form-group row total-shipping">
										<div class="col-6 name"><?= $enc->html( $this->translate( 'admin', 'Shipping' ) ) ?></div>
										<div class="col-6 value"><?= $enc->html( sprintf( $priceFormat, $this->number( $this->item->getPrice()->getCosts() ), $currency ) ) ?></div>
									</div>
									<?php if( $this->item->getPrice()->getTaxFlag() === true ) : ?>
										<div class="form-group row total-value">
											<div class="col-6 name"><?= $enc->html( $this->translate( 'admin', 'Total' ) ) ?></div>
											<div class="col-6 value"><?= $enc->html( sprintf( $priceFormat, $this->number( $this->item->getPrice()->getValue() + $this->item->getPrice()->getCosts() ), $currency ) ) ?></div>
										</div>
									<?php endif ?>
									<div class="form-group row total-tax">
										<div class="col-6 name">
											<?php if( $this->item->getPrice()->getTaxFlag() ) : ?>
												<?= $enc->html( $this->translate( 'admin', 'Incl. tax' ) ) ?>
											<?php else : ?>
												<?= $enc->html( $this->translate( 'admin', '+ Tax' ) ) ?>
											<?php endif ?>
										</div>
										<div class="col-6 value"><?= $enc->html( sprintf( $priceFormat, $this->number( $this->item->getPrice()->getTaxValue() ), $currency ) ) ?></div>
									</div>
									<?php if( $this->item->getPrice()->getTaxFlag() === false ) : ?>
										<div class="form-group row total-value">
											<div class="col-6 name"><?= $enc->html( $this->translate( 'admin', 'Total' ) ) ?></div>
											<div class="col-6 value"><?= $enc->html( sprintf( $priceFormat, $this->number( $this->item->getPrice()->getValue() + $this->item->getPrice()->getCosts() + $this->item->getPrice()->getTaxValue() ), $currency ) ) ?></div>
										</div>
									<?php endif ?>
								</div>
							</div>
						</div>
					<?php endif ?>

				</div>

				<?= $this->get( 'itemBody' ) ?>

			</div>

			<div class="item-actions">
				<?= $this->partial( $this->config( 'admin/jqadm/partial/itemactions', 'itemactions' ), ['params' => $params] ) ?>
			</div>
		</div>
	</form>

<?php endif ?>

<?php $this->block()->stop() ?>


<?= $this->render( $this->config( 'admin/jqadm/template/page', 'page' ) ) ?>
