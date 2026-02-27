(function ($) {


function create_new_location () {
	let $root = $("#app-locations-add_location"),
		$additive = $("#app-locations-new_location"),
		labels = _app_locations_data.model.labels,
		fields = _app_locations_data.model.fields,
		markup = ''
	;
	if ($additive.length) {$additive.remove();}

	markup += '<h3>' + labels.new_location + '</h3>';
	const $container = $("<div>", { id: "app-locations-new_location" });
	$container.append($("<h3>").text(labels.new_location));

	$.each(fields, function (field, label) {
		const $label = $("<label>", { "for": "app-location-" + field }).text(label);
		const $input = $("<input>", {
			type: "text",
			id: "app-location-" + field,
			value: ""
		});
		$label.append($input);
		$container.append($label);
	});

	$container.append(
		$("<button>", {
			"class": "button button-primary",
			type: "button",
			id: "app-locations-create_location"
		}).text(labels.add_location)
	);
	$container.append(
		$("<button>", {
			"class": "button button-secondary",
			type: "button",
			id: "app-locations-cancel_location"
		}).text(labels.cancel_editing)
	);

	$root.after($container);

	$("#app-locations-create_location").on("click", function () {
		const location = {},
			$submit = $("#app-locations-save_locations"),
			tmp = $submit.before('<input type="hidden" name="locations[]" id="app-locations-added_location" value="" />'),
			$location = $("#app-locations-added_location")
		;
		$.each(fields, function (field, label) {
			location[field] = $("#app-location-" + field).val();
		});
		$location.val(JSON.stringify(location));
		$submit.trigger("click");
	});
	$("#app-locations-cancel_location").on("click", function () {
		$("#app-locations-new_location").remove();
		return false;
	});

	return false;
}

function edit_location () {
	const $me = $(this),
		$root = $("#app-locations-add_location"),
		$additive = $("#app-locations-new_location"),
		$data = $me.parents('li').find("input:hidden"),
		data = $data.val() ? JSON.parse($data.val()) : {},
		labels = _app_locations_data.model.labels,
		fields = _app_locations_data.model.fields,
		markup = ''
	;
	if ($additive.length) {$additive.remove();}

	const $container = $("<div>", { id: "app-locations-new_location" });
	$container.append($("<h3>").text(labels.edit_location));

	$.each(fields, function (field, label) {
		const $label = $("<label>", { "for": "app-location-" + field }).text(label);
		const $input = $("<input>", {
			type: "text",
			id: "app-location-" + field,
			value: data[field] || ""
		});
		$label.append($input);
		$container.append($label);
	});

	$container.append(
		$("<button>", {
			"class": "button button-primary",
			type: "button",
			id: "app-locations-create_location"
		}).text(labels.save_location)
	);
	$container.append(
		$("<button>", {
			"class": "button button-secondary",
			type: "button",
			id: "app-locations-cancel_location"
		}).text(labels.cancel_editing)
	);

	$root.after($container);

	$("#app-locations-create_location").on("click", function () {
		const location = data,
			$submit = $("#app-locations-save_locations")
		;
		$.each(fields, function (field, label) {
			location[field] = $("#app-location-" + field).val();
		});
		$data.val(JSON.stringify(location));
		$submit.trigger("click");
	});
	$("#app-locations-cancel_location").on("click", function () {
		$("#app-locations-new_location").remove();
		return false;
	});

	return false;
}

function delete_location () {
	const $me = $(this),
		$li = $me.parents('li')
	;
	$li.remove();
	return false;
}

function save_inline_appointment_data (e, data, $ctx) {
	$ctx = $ctx.length ? $ctx : $("body");
	const $location = $ctx.find('[name="location"]'),
		location_id = ($location.length ? $location.val() : '')
	;
	data['location'] = location_id;
	return false;
}

// Init
$(function () {
	if ("undefined" === typeof _app_locations_data) {return false;}
	$("#app-locations-add_location").on("click", create_new_location);
	$("#app-locations-list .app-locations-delete").on("click", delete_location);
	$("#app-locations-list .app-locations-edit").on("click", edit_location);

	// Inline save
	$(document).on('app-appointment-inline_edit-save_data', save_inline_appointment_data);

});
})(jQuery);