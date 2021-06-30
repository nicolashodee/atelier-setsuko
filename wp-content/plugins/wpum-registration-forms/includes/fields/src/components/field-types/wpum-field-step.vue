<template>
	<div class="widget-description">
		<button type="button" class="widget-action" @click="onEditStep"><span class="dashicons dashicons-edit"></span></button>
		<button type="button" class="widget-action widget-action-delete" @click="onDeleteStep"><span class="dashicons dashicons-trash"></span></button>
		<div class="spinner is-active" v-if="loading"></div>
		<div v-else>{{ stepTitle }}</div>
	</div>
</template>

<script>
import axios from 'axios'
import StepFieldFormDialog from "./../dialogs/dialog-steps-field-form.vue"
import StepFieldDeleteDialog from "./../dialogs/dialog-steps-field-delete.vue"

export default {
	name: 'wpum-field-step',
	props: [ 'field' ],
	data(){
		return {
			index: -1,
			formId: 0,
			childFields: [],
			loading: true
		}
	},
	computed: {
		stepTitle(){
			return this.field.content && this.field.content.title ? this.field.content.title : this.field.name;
		}
	},
	methods: {
		onEditStep(){
			this.showStepFieldModal();
		},
		onDeleteStep(){
			this.showStepFieldDeleteModal();
		},
		showStepFieldModal(){

			this.$modal.show(
				StepFieldFormDialog ,
				{
					formId: this.formId,
					fieldId: this.field.id,
					fieldIndex: this.index,
					fieldTitle: this.field.content.title,
					fieldDesc: this.field.content.description,
					/**
					 * this method will be called in modal component to save data
					 */
					saveStep: async (data) => {
						this.$parent.$parent.selectedFields[this.index].content = data
						this.$parent.$parent.saveFields()
						this.saveField()
					}
				},
				{
					height: '500px',
				}
			);
		},
		showStepFieldDeleteModal(index){
			this.$modal.show(
				StepFieldDeleteDialog,
				{
					deleteStepsField: async () => {
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
		async saveField(){

			const formData = new FormData()
			formData.append('action', 'save_wpumrf_field_meta')
			formData.append("nonce", wpumRegistrationFormsEditor.saveFormNonce)
			formData.append('form_id', this.formId)
			formData.append('field_id', this.field.id)
			formData.append('field', JSON.stringify( Object.assign( Object.assign({},this.field), { index: this.index, content: { title: this.field.content.title, description: this.field.content.description } } ) ))

			const request = await axios.post(
				wpumRegistrationFormsEditor.ajax,
				formData
			)

			return request
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

		this.$parent.$on('sort', this.onSort)

		if( !this.field.hasOwnProperty('content') ){
			this.loadFieldContent()
		}
	},
	destroyed() {
		this.$parent.$off('sort', this.onSort)
	},
}
</script>
