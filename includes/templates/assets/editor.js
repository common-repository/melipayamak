(function() {
	tinymce.create('tinymce.plugins.melipayamak', {
		init : function(ed, url) {
			ed.addButton('melipayamak', {
				title : 'ملی پیامک',
				image : url + '/logo2.png',
				onclick : function() {
					ed.windowManager.open({
						file : url + "/dialog.php",
						width : 370,
						height : 180,
						inline : 1,
						popup_css : false
					})
				}
			});
		},
		createControl : function(n, cm) {
			return null;
		},
	});
	tinymce.PluginManager.add('melipayamak', tinymce.plugins.melipayamak);
})();
