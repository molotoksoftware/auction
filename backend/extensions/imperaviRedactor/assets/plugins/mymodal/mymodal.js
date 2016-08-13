if (typeof RedactorPlugins === 'undefined') var RedactorPlugins = {};

RedactorPlugins.mymodal = {

	init: function()
	{
		var callback = $.proxy(function()
		{
			this.selectionSave();

			$('#redactor_modal #mymodal-link').click($.proxy(function()
			{
				this.insertFromMyModal();
				return false;

			}, this));
		}, this);

		this.buttonAdd('mymodal', 'My Modal', $.proxy(function()
		{
			this.modalInit('My Modal', '#mymodal', 500, callback);
		}, this));

		this.buttonAddSeparatorBefore('mymodal');

	},
	insertFromMyModal: function(html)
	{
		this.selectionRestore();
		this.execCommand('inserthtml', '<b>Inserted from My Modal</b>');
		this.modalClose();
	}

}