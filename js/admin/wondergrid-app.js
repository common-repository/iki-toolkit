window.ikiToolkit = window.ikiToolkit || {};
window.ikiToolkit.ikiGridAdmin = window.ikiToolkit.ikiGridAdmin || {};
window.ikiToolkit.ikiGridAdmin.AbstractRowView = (function ($, w) {

	"use strict";

	return Backbone.View.extend({
		events: {
			"click .iki-duplicate": 'duplicateRow',
			"click .iki-remove": 'removeRow',
			"click .iki-options": 'toggleOptionsPanel',
			"click .iki-close-options": '_closePanel'
		},
		duplicateRow: function (e) {
			e.preventDefault();
			window.ikiToolkit.ikiGridAdmin.rowViewEvents.trigger('duplicateRow', this.model.clone(), this);
		},
		removeRow: function (e) {
			e.preventDefault();
			this.model.destroy();
		},
		toggleOptionsPanel: function (e) {
			e.preventDefault();
			var _self = this;
			if (this.optionsPanelOpen) {
				//close
				_self._closePanel();
			}
			else {
				//open
				this.$optionsPanel.show({
					duration: 'fast',
					complete: function () {
						_self.optionsPanelOpen = true;
					}
				});
			}

		},
		_closePanel: function () {
			var _self = this;
			this.$optionsPanel.hide({
				duration: 'fast',
				complete: function () {
					_self.optionsPanelOpen = false;
				}
			});
		}
	});

})(jQuery, window);


window.ikiToolkit = window.ikiToolkit || {};
window.ikiToolkit.ikiGridAdmin = window.ikiToolkit.ikiGridAdmin || {};
window.ikiToolkit.ikiGridAdmin.ClassicRowView = (function ($, w) {

	"use strict";

	return window.ikiToolkit.ikiGridAdmin.AbstractRowView.extend({


		tagName: 'div',
		template: _.template($('#iki-classic-row-tpl').html()),

		attributes: {
			class: 'iki-row-wrapper'
		},

		events: function () {
			return _.extend({}, window.ikiToolkit.ikiGridAdmin.AbstractRowView.prototype.events, {
				"click .iki-orientation-btn": 'changeOrientation',
				"click .iki-cell-btn": 'changeCells'
			})
		},
		initialize: function () {
			//this.listenTo(this.model, 'change', this.render);
			this.listenTo(this.model, 'change:cells', this.onCellChange);
			this.listenTo(this.model, 'change:orientation', this.onOrientationChange);
			this.listenTo(this.model, 'destroy', this.onDestroy);

			this.optionsPanelOpen = false;
		},
		changeOrientation: function (e) {
			e.preventDefault();
			this.model.set('orientation', e.currentTarget.value);

		},

		onOrientationChange: function (model, newValue) {
			this.modifyOrientation(newValue, model.previous('orientation'));
		},
		modifyOrientation: function (newVal, oldVal) {

			this.$orientationBtns.removeClass('iki-ui-selected');
			this.$orientationBtns.filter('[value="' + newVal + '"]').addClass('iki-ui-selected');

			this.$row.addClass('iki-row-orientation-' + newVal);

			if (oldVal) {

				this.$row.removeClass('iki-row-orientation-' + oldVal)
					.addClass('iki-row-orientation-' + newVal);
			}
			else {
				switch (newVal) {

					case 'portrait':
						this.$row.removeClass('iki-row-orientation-square iki-row-orientation-landscape');
						break;

					case 'square':
						this.$row.removeClass('iki-row-orientation-portfolio iki-row-orientation-landscape');
						break;

					case'landscape':
						this.$row.removeClass('iki-row-orientation-portfolio iki-row-orientation-square');
						break;

				}
			}

		},
		onCellChange: function (model, newValue) {

			this.modifyCells(newValue, model.previous('cells'));
		},
		changeCells: function (e) {
			var cellsVal = e.currentTarget.value;
			this.model.set('cells', cellsVal);

		},
		modifyCells: function (cellsNum, oldCellsNum) {

			this.$cellBtns.removeClass('iki-ui-selected');
			this.$cellBtns.filter('[value="' + cellsNum + '"]').addClass('iki-ui-selected');

			this.$row.empty();
			this.$row.removeClass('iki-row-cells-' + oldCellsNum).addClass('iki-row-cells-' + cellsNum);
			for (var i = 1; i <= cellsNum; i++) {
				var $column = this.$column.clone();
				this.$row.append($column);
			}
		},
		onDestroy: function () {
			this.remove();
		},
		render: function () {
			this.$el.html(this.template(this.model.toJSON()));

			this.$row = this.$('.iki-grid-row');
			this.$column = this.$('.iki-grid-thumb').clone();
			this.$optionsPanel = this.$('.iki-classic-row-ui');
			this.$cellBtns = this.$('.iki-cell-btn');
			this.$orientationBtns = this.$('.iki-orientation-btn');
			if (this.model.attributes.cells !== 1) {
				this.modifyCells(this.model.attributes.cells, 1);
			}
			this.modifyOrientation(this.model.attributes.orientation);
			return this;
		}
	});

})(jQuery, window);

window.ikiToolkit = window.ikiToolkit || {};
window.ikiToolkit.ikiGridAdmin = window.ikiToolkit.ikiGridAdmin || {};
window.ikiToolkit.ikiGridAdmin.GridRowView = (function ($, w) {

	"use strict";

	return Backbone.View.extend({

		events: {
			"click .iki-new-classic": "addClassicRow",
			"click .iki-new-mixed": "addMixedRow"
		},
		addClassicRow: function (options) {
			this.collection.add(new window.ikiToolkit.ikiGridAdmin.RowModel(), options);
		},
		addMixedRow: function () {

			this.collection.add(new window.ikiToolkit.ikiGridAdmin.RowModel({
				type: 'mixed',
				name: 'mixed-1',
				cells: 3
			}));

		},
		initialize: function () {

			//this.listenTo(this.collection, 'update', this.onCollectionUpdate);
			this.listenTo(this.collection, 'add', this.addOne);
			this.listenTo(this.collection, 'remove', this.onCollectionRemove);
			this.listenTo(this.collection, 'reset', this.render);
			this.listenTo(this.collection, 'change:cells', this.onModelCellChange);

			this.$insertNewRowUI = this.$('.iki-insert-new-ui-wrap');

			this.listenTo(window.ikiToolkit.ikiGridAdmin.rowViewEvents, 'duplicateRow', this.onDuplicateRow);

			this.$gridDataField = $('#iki_grid_data');

			this.$totalCells = $('#iki-total-cells');
			this.totalCellCount = 0;
			this.mixedRowsParsed = window.ikiToolkit.ikiGridAdmin.mixedRowsParsed;
			var _self = this;
			$('#post').on('submit', function () {
				_self.updateGridMetadata();
			})

		},

		onModelCellChange: function (model, newValue) {

			this.totalCellCount += Number(newValue) - Number(model.previousAttributes().cells);

			this._updateTotalCells(Number(this.totalCellCount));
		},
		onCollectionRemove: function (rowModel) {
			this.totalCellCount -= rowModel.attributes.cells;


			this._updateTotalCells(this.totalCellCount);

		},
		_updateTotalCells: function (num) {

			this.$totalCells.text(num);
		},
		onCollectionUpdate: function () {
			//no-op
		},
		updateGridMetadata: function () {

			if (this.collection.size() === 0) {
				this.addClassicRow({silent: true});
			}
			var s = JSON.stringify(this.collection);
			this.$gridDataField.attr('value', s);
		},
		onDuplicateRow: function (model, view) {

			this.$addAfter = view.$el;
			this.collection.add(model, {at: Number(view.$el.index()) + 1});

		},
		render: function () {
			this.addAll();
		},
		addOne: function (rowModel) {


			if (rowModel.attributes.type === 'classic') {
				var view = new window.ikiToolkit.ikiGridAdmin.ClassicRowView({
					model: rowModel,
					collection: this.collection
				});

				if (this.$addAfter) {
					this.$addAfter.after(view.render().el);
					this.$addAfter = null;
				}
				else {
					this.$insertNewRowUI.before(view.render().el);

				}
			}
			else if (rowModel.attributes.type === 'mixed') {

				var viewMixed = new window.ikiToolkit.ikiGridAdmin.MixedRowView({
					model: rowModel,
					collection: this.collection
				});

				if (this.$addAfter) {
					this.$addAfter.after(viewMixed.render().el);
					this.$addAfter = null;
				}
				else {
					this.$insertNewRowUI.before(viewMixed.render().el);

				}
			}
			this.totalCellCount += Number(rowModel.attributes.cells);

			this._updateTotalCells(this.totalCellCount);

		},
		addAll: function () {
			this.collection.each(this.addOne, this);
		},
		remove: function () {
			this.collection = null;
		}

	});
})(jQuery, window);


window.ikiToolkit = window.ikiToolkit || {};
window.ikiToolkit.ikiGridAdmin = window.ikiToolkit.ikiGridAdmin || {};
window.ikiToolkit.ikiGridAdmin.MixedRowView = (function ($, w) {

	"use strict";

	return window.ikiToolkit.ikiGridAdmin.AbstractRowView.extend({
		tagName: 'div',
		template: _.template($('#iki-mixed-row-tpl').html()),
		attributes: {
			class: 'iki-row-wrapper'
		},
		events: function () {
			return _.extend({},
				window.ikiToolkit.ikiGridAdmin.AbstractRowView.prototype.events,
				{
					"click .mixed-row-btn": 'changeRow'
				})
		},
		initialize: function () {
			this.listenTo(this.model, 'destroy', this.onDestroy);
			this.listenTo(this.model, 'change:name', this.onChangeRow);
			this.mixedRowsParsed = window.ikiToolkit.ikiGridAdmin.mixedRowsParsed;

		},
		changeRow: function (e) {
			e.preventDefault();
			var newRow = $(e.currentTarget).data('ikiRow');
			console.log("new row", newRow);

			var newCells = this.mixedRowsParsed[newRow].orientation.length;
			this.model.set('name', newRow);
			this.model.set('cells', newCells);
		},
		onChangeRow: function (model, newValue) {

			this.attachRowImage(newValue);
		},
		onDestroy: function () {
			console.log("removing elemen");
			this.remove();
		},
		render: function () {

			this.$el.html(this.template(this.model.toJSON()));

			this.$row = this.$('.iki-grid-row');
			this.$optionsPanel = this.$('.iki-classic-row-ui');

			this.$mixedRowBtns = this.$('.mixed-row-btn');

			this.$currentRowImg = this.$('.iki-current-row');

			this.attachRowImage(this.model.get('name'));
			return this;

		},
		attachRowImage: function (rowName) {
			var $selectedRowBtn = this.$mixedRowBtns.filter('[data-iki-row="' + rowName + '"]');

			this.$mixedRowBtns.removeClass('iki-selected');
			$selectedRowBtn.addClass('iki-selected');
			this.$currentRowImg = this.$('.iki-current-row');

			if ($selectedRowBtn.length) {
				var imgSrc = $selectedRowBtn.find('img').attr('src');

				this.$currentRowImg.attr('src', imgSrc);
				this.$currentRowImg.addClass($selectedRowBtn.data('iki-row')); // u principu OK

			}
		}
	});

})(jQuery, window);
jQuery(document).ready(function ($) {

	"use strict";

	window.ikiToolkit = window.ikiToolkit || {};
	window.ikiToolkit.ikiGridAdmin = window.ikiToolkit.ikiGridAdmin || {};

	var RowModel = window.ikiToolkit.ikiGridAdmin.RowModel = Backbone.Model.extend({
		defaults: {
			cells: 4,
			orientation: 'portrait',
			type: 'classic',
			name: null,
			condensed: false
		}
	});


	window.ikiToolkit.ikiGridAdmin.RowCollection = Backbone.Collection.extend(
		{
			model: window.ikiToolkit.ikiGridAdmin.RowModel
		}
	);

	var rowCollection = new window.ikiToolkit.ikiGridAdmin.RowCollection();


	var availableRows = JSON.parse(window.ikiAvailableMixedRows);
	var mixedRows = {};

	_.each(availableRows.mixed, function (value, index) {
		mixedRows[value.name] = value;

	});


	window.ikiToolkit.ikiGridAdmin.mixedRowsParsed = mixedRows;
	window.ikiToolkit.ikiGridAdmin.rowViewEvents = {};
	_.extend(window.ikiToolkit.ikiGridAdmin.rowViewEvents, Backbone.Events);

	new window.ikiToolkit.ikiGridAdmin.GridRowView({
		el: '#iki-grid-wrapper',
		collection: rowCollection
	});

	var defaultCollection = [];


	if (window.ikiGridMetaData && window.ikiGridMetaData.length > 0) {
		var gridMetaData = JSON.parse(window.ikiGridMetaData);

		_.each(gridMetaData, function (value, index) {
			if (value.type === 'classic') {
				//classic model
				defaultCollection.push(new RowModel({
					type: 'classic',//default
					cells: value.cells,
					orientation: value.orientation,
					name: null
				}))
			}
			else {
				//mixed model

				if (mixedRows[value.name]) {
					defaultCollection.push(new RowModel({
						type: 'mixed',
						name: value.name,
						orientation: mixedRows[value.name].orientation,
						//orientation: null,
						cells: mixedRows[value.name].orientation.length
					}))

				}
			}
		});

	}
	else {
		defaultCollection.push(new RowModel());
	}

	rowCollection.reset(defaultCollection);


});