function createEditor(elementId) {
	ClassicEditor
		.create(document.querySelector('#' + elementId), {
			toolbar: {
				items: [
					'heading',
					'|',
					'bold',
					'italic',
					'link',
					'bulletedList',
					'numberedList',
					'|',
					'outdent',
					'indent',
					'|',
					'blockQuote',
					'insertTable',
					'undo',
					'redo'
				]
			},
			language: 'en',
			table: {
				contentToolbar: [
					'tableColumn',
					'tableRow',
					'mergeTableCells'
				]
			},
			licenseKey: '',
		})
		.then(editor => {
			window.editor = editor;
		})
		.catch(error => {
			console.error(error);
		});
}