
// version 0.40.2, 2022/JUNE/2ND

(function ($) {

	$.fn.repeatable = function (userSettings) {

		/**
		 * Default settings
		 * @type {Object}
		 */
		var defaults = {
			addTrigger: ".add",
			deleteTrigger: ".delete",
			max: null,
      min: 0,
			template: null,
			itemContainer: ".field-group",
			beforeAdd: function () {},
			afterAdd: function (item) {},
			beforeDelete: function (item) {},
			afterDelete: function () {}
		};

		/**
		 * DOM element into which repeatable
		 * items will be added
		 * @type {jQuery object}
		 */
		var target = $(this);

		/**
		 * Blend passed user settings with defauly settings
		 * @type {array}
		 */
		var settings = $.extend({}, defaults, userSettings);

		/**
		 * Total templated items found on the page
		 * at load. These may be created by server-side
		 * scripts.
		 * @return null
		 */
		var total = function () {
		    calc_total = $(target).find(settings.itemContainer).length;
		    //console.log(calc_total); // DEBUGGING ONLY
			return calc_total;
		}();

		/**
		 * Iterator used to make each added
		 * repeatable element unique
		 * @type {Number}
		 */
		var i = total > 0 ? total : 0;


		/**
		 * Add an element to the target
		 * and call the callback function
		 * @param  object e Event
		 * @return null
		 */
		var addOne = function (e) {
			e.preventDefault();
			settings.beforeAdd.call(this);
			var item = createOne();
			settings.afterAdd.call(this, item);
		};

		/**
		 * Delete the parent element
		 * and call the callback function
		 * @param  object e Event
		 * @return null
		 */
		var deleteOne = function (e) {
			e.preventDefault();
			if (total === settings.min) return;
			var item = $(this).parents(settings.itemContainer).first();
			settings.beforeDelete.call(this, item);
			item.remove();
			total--;
			maintainAddBtn();
			settings.afterDelete.call(this);
		};

		/**
		 * Add an element to the target
		 * @return null
		 */
		var createOne = function() {
			var item = getUniqueTemplate();
			item.appendTo(target);
			total++;
			maintainAddBtn();
			return item;
		};

		/**
		 * Alter the given template to make
		 * each form field name unique
		 * @return {jQuery object}
		 */
		var getUniqueTemplate = function () {
			var template = $(settings.template).html();
			i = i + 1;
			template = template.replace(/{\?}/g, "new" + i); 	// {?} => iterated placeholder
			template = template.replace(/\{[^\?\}]*\}/g, ""); 	// {valuePlaceholder} => ""
			return $(template);
		};

		/**
		 * Determines if the add trigger
		 * needs to be disabled
		 * @return null
		 */
		var maintainAddBtn = function () {
			if (!settings.max) {
				return;
			}

			if (total === settings.max) {
				$(settings.addTrigger).attr("disabled", "disabled");
			} else if (total < settings.max) {
				$(settings.addTrigger).removeAttr("disabled");
			}
		};

		/**
		 * Setup the repeater
		 * @return null
		 */
		(function () {
			$(settings.addTrigger).on("click", addOne);
			$("form").on("click", settings.deleteTrigger, deleteOne);

			if (!total) {
				var toCreate = settings.min - total;
				for (var j = 0; j < toCreate; j++) {
					createOne();
				}
			}

		})();
	};

})(jQuery);
