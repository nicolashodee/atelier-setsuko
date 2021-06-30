<template>
	<div class="media-modal-content wpum-dialog" id="html-field-form-dialog">
		<button type="button" class="media-modal-close" @click="$emit('close')"><span class="media-modal-icon"><span class="screen-reader-text">Close panel</span></span></button>
		<div class="media-frame mode-select wp-core-ui">
			<div class="media-frame-title">
				<h1>HTML Field</h1>
			</div>
			<div class="media-frame-content">
				<form action="#" method="post" class="dialog-form">
                    <textarea name="html-content" v-model="htmlField" id="html-content" cols="30" rows="10"></textarea>
				</form>
			</div>
			<div class="media-frame-toolbar">
				<div class="media-toolbar">
					<div class="media-toolbar-primary search-form">
						<div class="spinner is-active" v-if="loading"></div>
						<button style="min-width:100px;" type="button" class="button media-button button-large" :disabled="loading" @click="$emit('close')">Cancel</button>
						<button style="min-width:100px;" type="button" class="button media-button button-primary button-large media-button-insert" :disabled="(loading || !htmlField)" @click="save">{{ !isUpdate ? "Save" : "Update" }}</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import Axios from 'axios'

export default {
	name: 'html-field-form-dialog',
	props: {
		formId: 0,
		fieldId: 0,
		saveHtmlContent: '',
		fieldContent: '',
		fieldIndex: 0
	},
    data() {
		return {
			loading: false,
			htmlField: ''
		}
	},
	computed: {
		/**
		 * Detects modal is for update or add
		 */
		isUpdate(){
			return this.fieldContent;
		}
	},
	methods: {
		/**
		 * Calls the callback to save the content
		 */
		async save(){
			this.loading = true;

			await this.saveHtmlContent(this.htmlField);

			this.loading = false;
			this.$emit('close');
		}
	},
	/**
	 * Sets up WYSIWYG editor and its content
	 */
    mounted(){
		this.htmlField = this.fieldContent;
		document.getElementById('html-content').value = this.htmlField;

        wp.editor.remove('html-content');
        wp.editor.initialize(
            'html-content',
            {
                tinymce: {
                    wpautop: true,
                    plugins : 'charmap colorpicker compat3x directionality fullscreen hr image lists media paste tabfocus textcolor wordpress wpautoresize wpdialogs wpeditimage wpemoji wpgallery wplink wptextpattern',
                    toolbar1: 'bold italic underline strikethrough | bullist numlist | blockquote hr wp_more | alignleft aligncenter alignright | link unlink | fullscreen | wp_adv',
					toolbar2: 'formatselect alignjustify forecolor | pastetext removeformat charmap | outdent indent | undo redo | wp_help',
					setup: (editor) => {
						editor.on('keyup change', () => {
							this.htmlField = editor.getContent();
						});
  					}
                },
                quicktags: true,
				//mediaButtons: true
            }
        );
	}
}
</script>
