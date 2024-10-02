/** Scrape reviews from Which? Trusted Traders
* ---------------------------------------------------------------------------------- **/
function strbits($string) {
	return strlen($string) * 8;
}

function DOMinnerHTML($element) {
	$innerHTML = "";
	$children = $element->childNodes;
	foreach ($children as $child) {
		$tmp_dom = new DOMDocument();
		$tmp_dom->appendChild($tmp_dom->importNode($child, true));
		$innerHTML .= trim($tmp_dom->saveHTML());
	}
	return $innerHTML;
}

function scrape_reviews_shortcode() {
	libxml_use_internal_errors(true);

	$data = file_get_contents("https://trustedtraders.which.co.uk/businesses/kitchensmart/?reviews_count=all&reviews_sort=when_posted__desc");

	$check_existing = array();
	$check_existing_args = array(
		'posts_per_page' => -1,
		'post_type' => 'review',
		'post_status' => ['publish', 'future', 'draft', 'pending'],
	);

	$check_existing_query = new WP_Query( $check_existing_args );
	if ( $check_existing_query->have_posts() ):
		while ( $check_existing_query->have_posts() ): $check_existing_query->the_post();
			$check_existing[] = get_field('review_id');
		endwhile;
		wp_reset_postdata();
	endif;

	if (strlen($data) > 350000) {
		$return = "";
		$matches = [];
		$dom = new DOMDocument();
		$dom->loadHTML($data);

		$divs = $dom->getElementsByTagName("div");
		foreach ($divs as $div) {
			if (!$div->hasAttribute("class")) {
				continue;
			}

			$class = explode(" ", $div->getAttribute("class"));

			if (in_array("review", $class)) {
				$innerHtml = DOMinnerHTML($div);
				$matches[$div->getAttribute("data-review-id")] =
					"<div>" . $innerHtml . "</div>";
			}
		}

		$review_arrays = [];
		$review_array = [];
		$review_dom = new DOMDocument();
		foreach ($matches as $review_id => $match) {
			if (!empty($review_id)) {
				$review_dom->loadHTML($match);
				$review_divs = $review_dom->getElementsByTagName("div");
				foreach ($review_divs as $review_div) {
					if (!$review_div->hasAttribute("class")) {
						continue;
					}
					$class_div = explode(
						" ",
						$review_div->getAttribute("class")
					);

					$review_array["review_id"] = $review_id;

					if (in_array("review-title", $class_div)) {
						$review_title__innerHtml = DOMinnerHTML($review_div);
						$review_array[
							"review_title"
						] = $review_title__innerHtml;
					}

					if (in_array("review-text", $class_div)) {
						$review_text__innerHtml = DOMinnerHTML($review_div);
						$review_text_cleaned = strip_tags(
							$review_text__innerHtml
						);
						$review_text_cleaned = preg_replace(
							"/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/",
							"\n",
							$review_text_cleaned
						);
						$review_array["review_text"] = $review_text_cleaned;
					}

					// $review_array["stars_customer-service"] = null;
					if (in_array("review-single-attribute", $class_div) and in_array("customer-service", $class_div)) {
						$review_stars__innerHtml = DOMinnerHTML($review_div);
						$review_stars_ratings = $review_stars__innerHtml;

						$stars_dom = new DOMDocument();
						$stars_dom->loadHTML($review_stars__innerHtml);
						$stars_divs = $stars_dom->getElementsByTagName("div");
						foreach ($stars_divs as $stars_div) {
							if (!$stars_div->hasAttribute("class")) {
								continue;
							}
							$class_stars_div = explode(
								" ",
								$stars_div->getAttribute("class")
							);
							if (in_array("review-attribute-field", $class_stars_div) and in_array("review-value", $class_stars_div)) {
								// $return .= '<pre>' . $review_title__innerHtml . ' stars_customer-service FOUND ->' . $stars_div->getAttribute("title") . '</pre><br />';
								$review_array[
									"stars_customer-service"
								] = $stars_div->getAttribute("title");
							}
						}
					} else {
						
					}

					// $review_array["stars_value"] = null;
					if (in_array("review-single-attribute", $class_div) and in_array("value", $class_div)) {
						$review_stars__innerHtml = DOMinnerHTML($review_div);
						$review_stars_ratings = $review_stars__innerHtml;

						$stars_dom = new DOMDocument();
						$stars_dom->loadHTML($review_stars__innerHtml);
						$stars_divs = $stars_dom->getElementsByTagName("div");
						foreach ($stars_divs as $stars_div) {
							if (!$stars_div->hasAttribute("class")) {
								continue;
							}
							$class_stars_div = explode(
								" ",
								$stars_div->getAttribute("class")
							);
							if (in_array("review-attribute-field", $class_stars_div) and in_array("review-value", $class_stars_div)) {
								// $return .= '<pre>' . $review_title__innerHtml . ' stars_value FOUND ->' . $stars_div->getAttribute("title") . '</pre><br />';
								$review_array[
									"stars_value"
								] = $stars_div->getAttribute("title");
							}
						}
					} else {
						
					}

					// $review_array["stars_quality"] = null;
					if (in_array("review-single-attribute", $class_div) and in_array("quality", $class_div)) {
						$review_stars__innerHtml = DOMinnerHTML($review_div);
						$review_stars_ratings = $review_stars__innerHtml;

						$stars_dom = new DOMDocument();
						$stars_dom->loadHTML($review_stars__innerHtml);
						$stars_divs = $stars_dom->getElementsByTagName("div");
						foreach ($stars_divs as $stars_div) {
							if (!$stars_div->hasAttribute("class")) {
								continue;
							}
							$class_stars_div = explode(
								" ",
								$stars_div->getAttribute("class")
							);
							if (in_array("review-attribute-field", $class_stars_div) and in_array("review-value", $class_stars_div)) {
								// $return .= '<pre>' . $review_title__innerHtml . ' stars_quality FOUND ->' . $stars_div->getAttribute("title") . '</pre><br />';
								$review_array[
									"stars_quality"
								] = $stars_div->getAttribute("title");
							}
						}
					} else {
						
					}
				}

				$review_spans = $review_dom->getElementsByTagName("span");
				foreach ($review_spans as $review_span) {
					if (!$review_span->hasAttribute("class")) {
						continue;
					}
					$class_span = explode(
						" ",
						$review_span->getAttribute("class")
					);

					if (in_array("review-reviewer-name", $class_span)) {
						$review_name__innerHtml = DOMinnerHTML($review_span);
						$review_array["review_name"] = $review_name__innerHtml;
					}

					if (in_array("review-timestamp", $class_span)) {
						$review_timestamp__innerHtml = DOMinnerHTML(
							$review_span
						);
						if (
							str_contains(
								$review_timestamp__innerHtml,
								"Posted on "
							)
						) {
							$review_posted_on_clean = str_replace(
								"Posted on ",
								"",
								$review_timestamp__innerHtml
							);
							$review_array[
								"posted_on"
							] = $review_posted_on_clean;
						} else {
							$review_array[
								"completed_on"
							] = $review_timestamp__innerHtml;
						}
					}
				}
				$review_arrays[$review_id] = $review_array;
			}
		}
	}

	$count_reviews = wp_count_posts("review");
	$review_count = [];
	if ($count_reviews) {
		$review_count["published_reviews"] = $count_reviews->publish;
		$review_count["future_reviews"] = $count_reviews->future;
		$review_count["future_draft"] = $count_draft->draft;
		$review_count["future_pending"] = $count_draft->pending;
	}
	$review_posts_total = array_sum($review_count);

	$return .=
		"Scraped Reviews from Which.co.uk: " . count($review_arrays) . "<br />";
	$return .= "Existing Reviews: " . $review_posts_total . "<br />";

	if (count($review_arrays) > $review_posts_total) {
		global $post;

		$args = [
			"posts_per_page" => -1,
			"post_type" => "review",
			"post_status" => ["publish", "future", "draft", "pending"],
		];

		$the_query = new WP_Query($args);
		if ($the_query->have_posts()):
			while ($the_query->have_posts()):
				$the_query->the_post();
				$to_scrape = [];
				$this_post_review_id = get_field("review_id");
				if (array_key_exists($this_post_review_id, $review_arrays)) {
					unset($review_arrays[$this_post_review_id]);
				}
			endwhile;
		endif;

		$return .=
			"<br />New Scrapes Found: " . count($review_arrays) . "<br />";

		foreach ($review_arrays as $review_array) {
			$post_date_format = str_replace(
				"/",
				"-",
				$review_array["posted_on"]
			);
			$post_date_time_newformat = date(
				"Y-m-d H:i:s",
				strtotime($post_date_format)
			);

			$completed_date_format = str_replace(
				"/",
				"-",
				$review_array["completed_on"]
			);
			$completed_date_time_newformat = date(
				"Y-m-d H:i:s",
				strtotime($completed_date_format)
			);

			$new_review = [
				"post_title" => $review_array["review_title"],
				"post_type" => "review",
				"post_status" => "publish",
				"post_category" => [1],
				"post_date" => $post_date_time_newformat,
				"post_author" => 2,
			];

			if( ! in_array( $review_array["review_id"], $check_existing ) ) {
				$the_review_post_id = wp_insert_post($new_review);
				update_field(
					"review_id",
					$review_array["review_id"],
					$the_review_post_id
				);
				update_field(
					"review_posted_on",
					$post_date_time_newformat,
					$the_review_post_id
				);
				update_field(
					"review_completed_on",
					$completed_date_time_newformat,
					$the_review_post_id
				);
				update_field(
					"review_content",
					$review_array["review_text"],
					$the_review_post_id
				);
				update_field(
					"review_name",
					$review_array["review_name"],
					$the_review_post_id
				);
				update_field(
					"review_star_customer_service",
					$review_array["stars_customer-service"],
					$the_review_post_id
				);
				update_field(
					"review_star_quality",
					$review_array["stars_quality"],
					$the_review_post_id
				);
				update_field(
					"review_star_value",
					$review_array["stars_value"],
					$the_review_post_id
				);

				$return .= "New Reviews Inserted: " . get_the_title($the_review_post_id) . " <a href='" . get_edit_post_link($the_review_post_id) . "' target='_blank'>(edit)</a><br />";
			}
			
		}
	} else {
		$return .=
			"<br />No action because no new review scrapes were found.<br />";
	}

	// $return .= '<pre>';
	// $return .= print_r($review_arrays, true);
	// $return .= '</pre>';
	// $return .= "<br /><br />";
	return $return;
}
add_shortcode("scrape_reviews", "scrape_reviews_shortcode");

function display_reviews_shortcode()
{
	$return = "";

	global $post;

	$args = [
		"posts_per_page"    => -1,
		"post_type"         => "review",
		"post_status"       => ["publish"],
		// 'meta_key'          => 'review_posted_on',
		// 'orderby'           => 'meta_value',
		// 'order'             => 'DESC'
	];

	$the_query = new WP_Query($args);
	if ($the_query->have_posts()):
		$stars_all = array();
		$stars_cs = array();
		$stars_qu = array();
		$stars_va = array();
		$reviews = array();
		while ($the_query->have_posts()):
			$the_query->the_post();
			$this_post_id = get_the_ID();

			$this_cs_key = "cs_" . $this_post_id;
			$stars_all[$this_cs_key] = get_field("review_star_customer_service");
			$stars_cs[$this_post_id] = get_field("review_star_customer_service");

			$this_qu_key = "qu_" . $this_post_id;
			$stars_all[$this_qu_key] = get_field("review_star_quality");
			$stars_qu[$this_post_id] = get_field("review_star_quality");

			$this_va_key = "va_" . $this_post_id;
			$stars_all[$this_va_key] = get_field("review_star_value");
			$stars_va[$this_post_id] = get_field("review_star_value");
			
			$reviews[$this_post_id] .= '
				<div class="review-item review">
					<div class="review-title">' . get_the_title() . '</div>
					<div class="review-contents">
						<div class="review-text">
							' . get_field('review_content') . '
						</div>
						<div class="review-ratings">
							<div class="review-full-attributes">
								<div class="review-attribute customer-service">
									<span class="review-attribute-field review-label review-attribute-customer-service">
										Customer Service
									</span>
									<div class="review-attribute-field review-stars review-attribute-stars">
										<i data-star="' . get_field('review_star_customer_service') . '"></i>
									</div>
								</div>
								<div class="review-attribute quality">
									<span class="review-attribute-field review-label review-attribute-customer-service">
										Quality
									</span>
									<div class="review-attribute-field review-stars review-attribute-stars">
										<i data-star="' . get_field('review_star_quality') . '"></i>
									</div>
								</div>
								<div class="review-attribute value">
									<span class="review-attribute-field review-label review-attribute-customer-service">
										Value
									</span>
									<div class="review-attribute-field review-stars review-attribute-stars">
										<i data-star="' . get_field('review_star_value') . '"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="review-details">
						<span class="review-reviewer-name">' . get_field('review_name') . '. </span>
						<span class="review-timestamp">Posted on ' . get_field('review_posted_on') . '</span>
						<span>, work completed </span><span class="review-timestamp">' . get_field('review_completed_on') . '</span>
					</div>
					<div class="review-recommended">Recommended</div>
				</div>
			';
		endwhile;
	endif;

	$stars_all = array_filter($stars_all);
	if (count($stars_all)) {
		$stars_average = array_sum($stars_all) / count($stars_all);
	}

	$stars_cs = array_filter($stars_cs);
	if (count($stars_cs)) {
		$stars_cs_average = array_sum($stars_cs) / count($stars_cs);
	}

	$stars_qu = array_filter($stars_qu);
	if (count($stars_qu)) {
		$stars_qu_average = array_sum($stars_qu) / count($stars_qu);
	}

	$stars_va = array_filter($stars_va);
	if (count($stars_va)) {
		$stars_va_average = array_sum($stars_va) / count($stars_va);
	}

	// $return .= '<pre>$stars_average:<br />';
	// $return .= number_format($stars_average + 0.1, 1);

	// $count_reviews = wp_count_posts("review");
	// $return .= "<br />Based on " . $count_reviews->publish . " reviews.<br />";

	// $return .=
	//     "<br />Average customer service stars: " .
	//     number_format($stars_cs_average, 1) .
	//     "<br />";
	// $return .=
	//     "<br />Average quality stars: " .
	//     number_format($stars_qu_average, 1) .
	//     "<br />";
	// $return .=
	//     "<br />Average value stars: " .
	//     number_format($stars_va_average, 1) .
	//     "<br />";

	// $return .= "</pre>";

	$count_reviews = wp_count_posts("review");

	$return .= '<div class="which-reviews-container">';
	$return .= '
		<div class="reviews-summary">
			<div class="reviews-overall-container">
				<div class="overall-count">
					' . number_format($stars_average, 1) . '
				</div>
				<div class="overall-stars">
				<i data-star="' . number_format($stars_average, 1) . '"></i>
				</div>
				<div class="overall-total">
					Based on ' . $count_reviews->publish . ' reviews.
				</div>
			</div>
			<div class="average-ratings-container">
				<div class="revew-full-attributes">
					<div class="review-attribute customer-service">
						<span class="review-attribute-field review-label review-attribute-customer-service">
							Customer Service
						</span>
						<div class="review-attribute-field review-figure review-attribute-figure">
							' . number_format($stars_cs_average, 1) . '
						</div>
						<div class="review-attribute-field review-stars review-attribute-stars">
						<i data-star="' . number_format($stars_cs_average, 1) . '"></i>
						</div>
					</div>
					<div class="review-attribute quality">
						<span class="review-attribute-field review-label review-attribute-quality">
							Quality
						</span>
						<div class="review-attribute-field review-figure review-attribute-figure">
							' . number_format($stars_qu_average, 1) . '
						</div>
						<div class="review-attribute-field review-stars review-attribute-stars">
						<i data-star=' . number_format($stars_qu_average, 1) . '></i>
						</div>
					</div>
					<div class="review-attribute value">
						<span class="review-attribute-field review-label review-attribute-value">
							Value
						</span>
						<div class="review-attribute-field review-figure review-attribute-figure">
							' . number_format($stars_va_average, 1) . '
						</div>
						<div class="review-attribute-field review-stars review-attribute-stars">
						<i data-star="' . number_format($stars_va_average, 1) . '"></i>
						</div>
					</div>
				</div>
			</div>
			<div class="total-recommended-container">
				<div class="reviews-thumbs-up"><img src="https://kitchensmart.uk.com/wp-content/uploads/2024/05/thumbs-up.svg" /></div>
				<div class="reviews-recommended-heading">Recommended</div>
				<div class="reviews-recommended-count">by 99% of customers</div>
			</div>
		</div>
	';

	foreach( $reviews as $review ) {
		$return .= $review;
	}

	$return .= '</div>';

	return $return;
}
add_shortcode("display_reviews", "display_reviews_shortcode");

function reviews_display_css() {
	echo '<style>
		.which-reviews-container {
			max-width: 1000px;
			margin: 0 auto;
		}
		.reviews-summary {
			display: flex;
			justify-content: center;
			align-items: center;
			margin: 25px auto 50px;
			padding: 25px 15px;
			background-color: #f8f8f8;
		}

		.reviews-overall-container {
			display: flex;
			flex-direction: column;
			width: 260px;
			text-align: center;
			padding: 22px 35px;
			align-items: center;
			max-width: 100%;
			justify-content: center;
		}

		.reviews-summary > div {
			/* width: 150px; */
		}

		.overall-count {
			font-size: 51px;
			text-align: center;
			font-weight: bold;
			color: #E81C30;
			padding: 15px 0;
		}

		.overall-stars {
			line-height: 33px;
		}

		.average-ratings-container {
			padding: 45px 35px;
			border-left: 1px solid #eaeaea;
			border-right: 1px solid #eaeaea;
			display: flex;
			justify-content: center;
			width: 530px;
			max-width: 100%;
			/* align-items: center; */
		}

		.review-attribute {
			display: flex;
			font-weight: 500;
			align-items: center;
		}

		.review-item.review .review-full-attributes .review-attribute {
			height: 32px;
		}

		.review-attribute > span {
			width: 160px;
		}

		.review-figure {
			width: 40px;
		}

		.review-attribute-field.review-stars {
			width: 170px;
		}

		.total-recommended-container {
			width: 270px;
			text-align: center;
			max-width: 100%;
			padding: 22px 35px;
		}

		.review-item {
			border: 1px solid #f8f8f8;
			padding: 25px;
			margin: 0 auto 25px;
		}
		
		.review-title {
			font-size: 25px;
			font-weight: 600;
			padding-bottom: 15px;
		}
		
		.review-contents {
			display: flex;
			justify-content: space-between;
			gap: 25px;
		}

		.review-details {
			margin: 15px 0;
		}
		
		.review-recommended:before {
			content: "";
			background-image: url(https://kitchensmart.uk.com/wp-content/uploads/2024/05/thumbs-up.svg);
			background-size: contain;
			background-repeat: no-repeat;
			width: 30px;
			height:30px;
			display: inline-block;
			position: relative;
			top: 7px;
			margin-right: 7px;
		}

		span.review-timestamp {
			margin-right: -3px;
		}

		@media (max-width:767px) {
			.reviews-summary {
				flex-direction: column;
			}
			
			.average-ratings-container {
				border-color: #f8f8f8;
			}
			
			.review-item.review .review-contents {
				display: flex;
				flex-direction: column;
				justify-content: center;
				align-items: center;
			}

			.review-item.review .review-full-attributes .review-attribute {
				height: 32px;
			}
			
			.review-attribute {
				flex-direction: column;
				justify-content: center;
				align-items: center;
				text-align: center;
				margin-bottom: 32px;
			}
			
			.review-attribute-field.review-stars i {
				position: relative;
				top: -25px;
			}
			
			.review-attribute-field.review-stars {
				margin-bottom: -40px;
			}
		}                

		[data-star] {
		text-align:left;
		font-style:normal;
		font-size: 34px;
		display:inline-block;
		position: relative;
		unicode-bidi: bidi-override;
		top: -2px;
		}
		[data-star]::before { 
		display:block;
		content: "★★★★★";
		color: #eee;
		}
		[data-star]::after {
		white-space:nowrap;
		position:absolute;
		top:0;
		left:0;
		content: "★★★★★";
		width: 0;
		color: #e81c30;
		overflow:hidden;
		height:100%;
		}

		[data-star^="0.1"]::after{width:2%}
		[data-star^="0.2"]::after{width:4%}
		[data-star^="0.3"]::after{width:6%}
		[data-star^="0.4"]::after{width:8%}
		[data-star^="0.5"]::after{width:10%}
		[data-star^="0.6"]::after{width:12%}
		[data-star^="0.7"]::after{width:14%}
		[data-star^="0.8"]::after{width:16%}
		[data-star^="0.9"]::after{width:18%}
		[data-star^="1"]::after{width:20%}
		[data-star^="1.1"]::after{width:22%}
		[data-star^="1.2"]::after{width:24%}
		[data-star^="1.3"]::after{width:26%}
		[data-star^="1.4"]::after{width:28%}
		[data-star^="1.5"]::after{width:30%}
		[data-star^="1.6"]::after{width:32%}
		[data-star^="1.7"]::after{width:34%}
		[data-star^="1.8"]::after{width:36%}
		[data-star^="1.9"]::after{width:38%}
		[data-star^="2"]::after{width:40%}
		[data-star^="2.1"]::after{width:42%}
		[data-star^="2.2"]::after{width:44%}
		[data-star^="2.3"]::after{width:46%}
		[data-star^="2.4"]::after{width:48%}
		[data-star^="2.5"]::after{width:50%}
		[data-star^="2.6"]::after{width:52%}
		[data-star^="2.7"]::after{width:54%}
		[data-star^="2.8"]::after{width:56%}
		[data-star^="2.9"]::after{width:58%}
		[data-star^="3"]::after{width:60%}
		[data-star^="3.1"]::after{width:62%}
		[data-star^="3.2"]::after{width:64%}
		[data-star^="3.3"]::after{width:66%}
		[data-star^="3.4"]::after{width:68%}
		[data-star^="3.5"]::after{width:70%}
		[data-star^="3.6"]::after{width:72%}
		[data-star^="3.7"]::after{width:74%}
		[data-star^="3.8"]::after{width:76%}
		[data-star^="3.9"]::after{width:78%}
		[data-star^="4"]::after{width:80%}
		[data-star^="4.1"]::after{width:82%}
		[data-star^="4.2"]::after{width:84%}
		[data-star^="4.3"]::after{width:86%}
		[data-star^="4.4"]::after{width:88%}
		[data-star^="4.5"]::after{width:90%}
		[data-star^="4.6"]::after{width:92%}
		[data-star^="4.7"]::after{width:94%}
		[data-star^="4.8"]::after{width:96%}
		[data-star^="4.9"]::after{width:98%}
		[data-star^="5"]::after{width:100%}

		.reviews-thumbs-up img {
			display: block;
			margin: 0 auto 15px auto;
			width: 60px;
		}

		.reviews-recommended-heading {
			font-weight: bold;
			font-size: 21px;
		}
	</style>';
}
add_action('wp_head', 'reviews_display_css', 100);
/** END - Scrape reviews from Which? Trusted Traders
* ---------------------------------------------------------------------------------- **/
