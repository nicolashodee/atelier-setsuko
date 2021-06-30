<template>
	<div class="media-modal-content wpum-dialog" id="html-field-form-dialog">
		<button type="button" class="media-modal-close" @click="$emit('close')"><span class="media-modal-icon"><span class="screen-reader-text">Close panel</span></span></button>
		<div class="media-frame mode-select wp-core-ui">
			<div class="media-frame-title">
				<h1>Step</h1>
			</div>
			<div class="media-frame-content">
				<form action="#" method="post" class="dialog-form">
					<input type="text" v-model="title" id="steps-title" placeholder="Step Title">
                    <textarea name="html-content" v-model="description" id="steps-desc" cols="30" rows="10" placeholder="Step Description"></textarea>
				</form>
			</div>
			<div class="media-frame-toolbar">
				<div class="media-toolbar">
					<div class="media-toolbar-primary search-form">
						<div class="spinner is-active" v-if="loading"></div>
						<button style="min-width:100px;" type="button" class="button media-button button-large" :disabled="loading" @click="$emit('close')">Cancel</button>
						<button style="min-width:100px;" type="button" class="button media-button button-primary button-large media-button-insert" :disabled="(loading)" @click="save">{{ !isUpdate ? "Save" : "Update" }}</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import Axios from 'axios'

export default {
	name: 'steps-field-form-dialog',
	props: {
		formId: 0,
		fieldId: 0,
		saveStep: '',
		fieldTitle: '',
		fieldDesc: '',
		fieldIndex: 0
	},
    data() {
		return {
			loading: false,
			title: '',
			description: ''
		}
	},
	computed: {
		/**
		 * Detects modal is for update or add
		 */
		isUpdate(){
			return this.fieldTitle;
		}
	},
	methods: {
		/**
		 * Calls the callback to save the content
		 */
		async save(){
			this.loading = true;

			await this.saveStep({title: this.title, description: this.description});

			this.loading = false;
			this.$emit('close');
		}
	},
	/**
	 * Sets up WYSIWYG editor and its content
	 */
    mounted(){
		this.title = this.fieldTitle
		this.description = this.fieldDesc
	}
}
</script>
