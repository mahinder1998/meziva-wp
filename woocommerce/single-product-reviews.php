<?php
defined( 'ABSPATH' ) || exit;

global $product;

if ( ! comments_open() ) {
	return;
}

$product_id = $product ? $product->get_id() : get_the_ID();

/**
 * Direct approved review comments for this product
 */
$mz_review_args = array(
	'post_id' => $product_id,
	'status'  => 'approve',
	'type'    => 'review',
	'orderby' => 'comment_date_gmt',
	'order'   => 'DESC',
);

$mz_reviews = get_comments( $mz_review_args );
$mz_review_count = is_array( $mz_reviews ) ? count( $mz_reviews ) : 0;
?>

<div id="reviews" class="woocommerce-Reviews mz-mt-10">
	<div id="comments" class="mz-space-y-6">
		<h2 class="woocommerce-Reviews-title mz-text-[22px] md:mz-text-[28px] mz-font-extrabold mz-text-gray-900 mz-leading-tight">
			<?php
			$count = $product->get_review_count();

			if ( $count && wc_review_ratings_enabled() ) {
				$reviews_title = sprintf(
					esc_html( _n( '%1$s review for %2$s', '%1$s reviews for %2$s', $count, 'woocommerce' ) ),
					esc_html( $count ),
					'<span>' . get_the_title() . '</span>'
				);
				echo wp_kses_post( apply_filters( 'woocommerce_reviews_title', $reviews_title, $count, $product ) );
			} else {
				esc_html_e( 'Reviews', 'woocommerce' );
			}
			?>
		</h2>

		<?php if ( $mz_review_count > 0 ) : ?>
			<ol class="commentlist mz-list-none mz-m-0 mz-p-0 mz-space-y-4">
				<?php
				foreach ( $mz_reviews as $comment ) {
					if ( function_exists( 'mz_wc_review_card_callback' ) ) {
						mz_wc_review_card_callback( $comment, array(), 1 );
					}
				}
				?>
			</ol>
		<?php else : ?>
			<p class="woocommerce-noreviews mz-text-gray-600 mz-text-base">
				<?php esc_html_e( 'There are no reviews yet.', 'woocommerce' ); ?>
			</p>
		<?php endif; ?>
	</div>

	<?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->get_id() ) ) : ?>
		<div id="review_form_wrapper" class="">
			<div id="review_form">
				<?php
				$commenter    = wp_get_current_commenter(); 
				$comment_form = array(
					'title_reply'         => have_comments() ? esc_html__( 'Add a review', 'woocommerce' ) : sprintf( esc_html__( 'Be the first to review &ldquo;%s&rdquo;', 'woocommerce' ), get_the_title() ),
					'title_reply_to'      => esc_html__( 'Leave a Reply to %s', 'woocommerce' ),
					'title_reply_before'  => '<span id="reply-title" class="comment-reply-title mz-block mz-text-[22px] md:mz-text-[28px] mz-font-extrabold mz-text-gray-900 mz-leading-tight mz-mb-5" role="heading" aria-level="3">',
					'title_reply_after'   => '</span>',
					'comment_notes_after' => '',
					'label_submit'        => esc_html__( 'Submit', 'woocommerce' ),
					'logged_in_as'        => '',
					'class_submit'        => 'submit mz-inline-flex mz-items-center mz-justify-center mz-h-[50px] mz-min-w-[160px] mz-px-6 mz-rounded-xl mz-bg-brand-accent mz-text-white mz-font-bold mz-text-[16px] hover:mz-opacity-90 mz-transition mz-border-0',
					'comment_field'       => '',
				);

				$name_email_required = (bool) get_option( 'require_name_email', 1 );

				$fields = array(
					'author' => array(
						'label'        => __( 'Name', 'woocommerce' ),
						'type'         => 'text',
						'value'        => $commenter['comment_author'],
						'required'     => $name_email_required,
						'autocomplete' => 'name',
					),
					'email'  => array(
						'label'        => __( 'Email', 'woocommerce' ),
						'type'         => 'email',
						'value'        => $commenter['comment_author_email'],
						'required'     => $name_email_required,
						'autocomplete' => 'email',
					),
				);

				$comment_form['fields'] = array();

				foreach ( $fields as $key => $field ) {
					$field_html  = '<p class="comment-form-' . esc_attr( $key ) . ' mz-mb-4">';
					$field_html .= '<label for="' . esc_attr( $key ) . '" class="mz-block mz-text-sm mz-font-semibold mz-text-gray-900 mz-mb-2">';
					$field_html .= esc_html( $field['label'] );

					if ( $field['required'] ) {
						$field_html .= ' <span class="required">*</span>';
					}

					$field_html .= '</label>';
					$field_html .= '<input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="' . esc_attr( $field['type'] ) . '" autocomplete="' . esc_attr( $field['autocomplete'] ) . '" value="' . esc_attr( $field['value'] ) . '" class="mz-w-full mz-h-12 mz-rounded-xl mz-border mz-border-gray-300 mz-px-4 mz-outline-none focus:mz-border-brand-accent" ' . ( $field['required'] ? 'required' : '' ) . ' />';
					$field_html .= '</p>';

					$comment_form['fields'][ $key ] = $field_html;
				}

				$account_page_url = wc_get_page_permalink( 'myaccount' );
				if ( $account_page_url ) {
					$comment_form['must_log_in'] = '<p class="must-log-in mz-text-sm mz-text-gray-700">' . sprintf(
						esc_html__( 'You must be %1$slogged in%2$s to post a review.', 'woocommerce' ),
						'<a href="' . esc_url( $account_page_url ) . '" class="mz-text-brand-accent mz-font-semibold">',
						'</a>'
					) . '</p>';
				}

				if ( wc_review_ratings_enabled() ) {
					$comment_form['comment_field'] .= '
						<p class="comment-form-rating mz-mb-4">
							<label for="rating" id="comment-form-rating-label" class="mz-block mz-text-sm mz-font-semibold mz-text-gray-900 mz-mb-2">' . esc_html__( 'Your rating', 'woocommerce' ) . ( wc_review_ratings_required() ? ' <span class="required">*</span>' : '' ) . '</label>
							<select name="rating" id="rating" required class="mz-w-full mz-h-12 mz-rounded-xl mz-border mz-border-gray-300 mz-px-4 mz-outline-none focus:mz-border-brand-accent">
								<option value="">' . esc_html__( 'Rate&hellip;', 'woocommerce' ) . '</option>
								<option value="5">' . esc_html__( 'Perfect', 'woocommerce' ) . '</option>
								<option value="4">' . esc_html__( 'Good', 'woocommerce' ) . '</option>
								<option value="3">' . esc_html__( 'Average', 'woocommerce' ) . '</option>
								<option value="2">' . esc_html__( 'Not that bad', 'woocommerce' ) . '</option>
								<option value="1">' . esc_html__( 'Very poor', 'woocommerce' ) . '</option>
							</select>
						</p>';
				}

				$comment_form['comment_field'] .= '
					<p class="comment-form-comment mz-mb-4">
						<label for="comment" class="mz-block mz-text-sm mz-font-semibold mz-text-gray-900 mz-mb-2">' . esc_html__( 'Your review', 'woocommerce' ) . ' <span class="required">*</span></label>
						<textarea id="comment" name="comment" cols="45" rows="8" required class="mz-w-full mz-rounded-xl mz-border mz-border-gray-300 mz-px-4 mz-py-3 mz-outline-none focus:mz-border-brand-accent"></textarea>
					</p>';

				comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
				?>
			</div>
		</div>
	<?php else : ?>
		<p class="woocommerce-verification-required mz-text-gray-600 mz-mt-6">
			<?php esc_html_e( 'Only logged in customers who have purchased this product may leave a review.', 'woocommerce' ); ?>
		</p>
	<?php endif; ?>

	<div class="mz-clear-both"></div>
</div> 