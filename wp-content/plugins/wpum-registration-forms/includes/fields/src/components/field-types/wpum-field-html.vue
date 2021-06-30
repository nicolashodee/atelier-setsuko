<template>
	<div class="widget-description">
		<button type="button" class="widget-action" @click="onEditHtmlContent"><span class="dashicons dashicons-edit"></span></button>
		<button type="button" class="widget-action widget-action-delete" @click="onDeleteHtmlContent"><span class="dashicons dashicons-trash"></span></button>
		<div class="spinner is-active" v-if="loading"></div>
		<div v-html="field.content" v-else></div>
	</div>
</template>

<script>
import axios from 'axios'
import HtmlFieldFormDialog from "./../dialogs/dialog-html-field-form.vue"
import HtmlFieldDeleteDialog from "./../dialogs/dialog-html-field-delete.vue"

export default {
	name: 'wpum-field-html_content',
	props: [ 'field' ],
	data(){
		return {
			index: -1,
			formId: 0,
			childFields: [],
			loading: true
		}
	},
	methods: {
		onEditHtmlContent(){
			this.showHtmlFieldModal();
		},
		onDeleteHtmlContent(){
			this.showHtmlFieldDeleteModal();
		},
		showHtmlFieldModal(){

			this.$modal.show(
				HtmlFieldFormDialog ,
				{
					formId: this.formId,
					fieldId: this.field.id,
					fieldIndex: this.index,
					fieldContent: this.field.content,
					/**
					 * this method will be called in modal component to save data
					 */
					saveHtmlContent: async (html) => {
						this.$parent.$parent.selectedFields[this.index].content = html
						this.$parent.$parent.saveFields()
						this.saveField()
					}
				},
				{
					height: '500px',
				}
			);
		},
		showHtmlFieldDeleteModal(index){
			this.$modal.show(
				HtmlFieldDeleteDialog,
				{
					deleteHtmlField: async () => {
						this.$parent.$parent.selectedFields.splice(this.index, 1);
						this.$parent.$parent.saveFields();
					}
				},
				{
					height: '200px',
					width: '350px'
				}
			)
		},
		initDragEvent(){
			this.$parent.$on('sort', this.onSort);
		},
		async loadFieldContent(){
			const formData = new FormData();
			formData.append("action", "get_wpumrf_field_content")
			formData.append("nonce", wpumRegistrationFormsEditor.saveFormNonce)
			formData.append("form_id", this.formId)
			formData.append("field_id", this.field.id)

			return axios.post(
				wpumRegistrationFormsEditor.ajax,
				formData
			)
			.then((response) => {
				const lists = response.data.data
				lists.forEach((field) => this.$set(this.$parent.$parent.selectedFields[field.index], 'content', field.content))

				this.loading = false
			})
			.catch((e) => {
				console.log(e)

				this.loading = false
			});
		},
		async saveField(){

			const formData = new FormData()
			formData.append('action', 'save_wpumrf_field_meta')
			formData.append("nonce", wpumRegistrationFormsEditor.saveFormNonce)
			formData.append('form_id', this.formId)
			formData.append('field_id', this.field.id)
			formData.append('field', JSON.stringify( Object.assign( { index: this.index }, this.field ) ))

			const request = await axios.post(
				wpumRegistrationFormsEditor.ajax,
				formData
			)

			return request
		},
		async onSort(){
			while( this.$parent.$parent.loading ){
				// Pause the scipt execution
				await new Promise(resolve => setTimeout(resolve, 50))
			}
			this.saveField()
		}
	},
	async created(){

		this.formId  = this.$parent.$parent.formID
		this.loading = false
	},
	async mounted() {

		this.index = window.jQuery(this.$el.closest('.widget')).index()

		this.initDragEvent()

		if( !this.field.hasOwnProperty('content') ){
			await this.loadFieldContent()
		}
	},
	destroyed() {
		this.$parent.$off('sort', this.onSort);
	}
}
</script>
