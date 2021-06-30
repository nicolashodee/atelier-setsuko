<template>
	<p style="text-align:right">
		<button class="button" @click="onAddStepContent">Add Step</button>
		<button class="button" @click="onAddHtmlContent">Add HTML</button>

		<modals-container/>
	</p>
</template>

<script>
import HtmlFieldFormDialog from "./dialogs/dialog-html-field-form.vue";
import StepFieldFormDialog from "./dialogs/dialog-steps-field-form.vue";
import HtmlFieldDeleteDialog from "./dialogs/dialog-html-field-delete.vue";
import dialogEditField from "./../../../../../wp-user-manager/src/fields-editor/dialogs/dialog-edit-field.vue";
import Axios from "axios";

export default {
	name: "form-actions",
	data: () => {
		return {
			id: 0,
			name: '',
			htmlContent: '',
			instances: [],
			activeInstance: -1,
			stepField: {}
		}
	},
	computed: {
		/**
		 * fetch selected fields instantly
		 */
		selectedFields(){
			return this.$parent.selectedFields;
		}
	},
	methods: {
		/**
		 * On adding a html field
		 */
		onAddHtmlContent(e){
			this.$modal.show(
				HtmlFieldFormDialog ,
				{
					formId: this.$parent.formID,
					fieldId: this.id,
					fieldIndex: -1,
					fieldContent: '',
					/**
					 * this method will be called in modal component to save data
					 */
					saveHtmlContent: async (html) => {
						this.$parent.selectedFields.push({
							id: this.id,
							name: this.name,
							content: html,
							type: 'html_content'
						})

						await this.saveParentFields();

						this.saveHtmlField()
					}
				},
				{
					height: '500px',
				}
			);
		},
		/**
		 * On custom fields edit
		 */
		onCustomFieldEdit(e){
			const widgetAction = e.path ? e.path.find( el => el.classList && el.classList.contains( 'widget-action' ) ) : '';
			if( !widgetAction ) return;

			this.showCustomFieldModal( this.getFieldIndex( widgetAction ) );
		},
		/**
		 * Showing a modal to edit custom field settings
		 */
		showCustomFieldModal(index){
			const field = this.$parent.selectedFields[index];
			if( !field ) return;

			if( [this.id, this.stepField.id].includes(field.id)  ) return;

			this.$modal.show(
				dialogEditField,
				{
					field_id: field.id,
					field_name: field.name,
					field_type: field.type,
					primary_id: field.default_id,
					/**
					 * Pass a function to the component so we can
					 * then update the app status from the child component response.
					 */
					updateStatus:(status) => {
						if( status == 'error' ) {
							this.showError( wpumFieldsEditor.labels.error_general )
						} else {
							this.showSuccess()
						}
					}
				},{
					height: '80%',
					width: field.type === 'repeater' ? '80%' : '60%'
				}
			)
		},
		/**
		 * Fetches fields data and inject to
		 * parent component data
		 */
		async loadFieldData(){

			const formData = new FormData();
			formData.append("action", "get_wpumrf_fields");
			formData.append("nonce", wpumRegistrationFormsEditor.saveFormNonce);

			return Axios.post(
				wpumRegistrationFormsEditor.ajax,
				formData
			)
			.then((response) => {

				const htmlField = response.data.data.find( field => field.type === 'html_content' );
				if( htmlField ){
					this.id   = htmlField.id;
					this.name = htmlField.name;
				}

				const stepField = response.data.data.find( field => field.type === 'step' );
				if( stepField ){
					this.stepField = { id: stepField.id, name: stepField.name }
				}
			})
			.catch((e) => {
				console.log(e);
			});
		},
		/**
		 * Renders widget title
		 */
		renderWidgetTop(widget, field){

			if( widget.querySelector( '.widget-title-action' ) ){
				return;
			}

			const widgetTopEl = widget.querySelector('.widget-top');

			if( !widgetTopEl ){
				return;
			}

			const widgetTitleAction = document.createElement('div');
			widgetTitleAction.classList.add('widget-title-action');

			const widgetActionEdit = document.createElement('button');
			widgetActionEdit.setAttribute('type', 'button');
			widgetActionEdit.classList.add('widget-action');

			const widgetActionEditIcon = document.createElement('span');
			widgetActionEditIcon.classList.add('dashicons');
			widgetActionEditIcon.classList.add('dashicons-edit');

			widgetActionEdit.append(widgetActionEditIcon);
			widgetTitleAction.append(widgetActionEdit);

			widgetTopEl.prepend(widgetTitleAction);

			widget.removeEventListener( 'click', this.onCustomFieldEdit, true );
			widget.addEventListener( 'click', this.onCustomFieldEdit );
		},
		/**
		 * Triggers parent component success message
		 */
		showSuccess(){
			this.$parent.showMessage = true;
			this.$parent.messageStatus  = 'success';
			this.$parent.messageContent = wpumFieldsEditor.success_message;
			this.$parent.resetNotice();
		},
		/**
		 * Triggers parent component error message
		 */
		showError(){
			this.$parent.showMessage = true;
			this.$parent.messageStatus  = 'error';
			this.$parent.messageContent = wpumFieldsEditor.labels.error_general;
			this.$parent.resetNotice();
		},
		/**
		 * Helper to find element index in droppable fields
		 */
		getFieldIndex(element){
			return window.jQuery(element.closest('.widget')).index()
		},
		getNewlyAddedComponent(type){
			const draggable  = this.$parent.$children.find( component => component.$el.classList.contains('droppable-fields') )
			const components = draggable.$children.filter( component => component.field && component.field.type === type  )

			return components.length ? components[components.length - 1] : false
		},
		saveHtmlField(){
			const htmlComponent = this.getNewlyAddedComponent( 'html_content' )

			if( htmlComponent ){
				htmlComponent.saveField()
			}
		},
		async saveParentFields(){
			this.$parent.saveFields()

			while( this.$parent.loading ){
				// Pause the scipt execution
				await new Promise(resolve => setTimeout(resolve, 50));
			}
		},
		onAddStepContent(){
			this.$modal.show(
				StepFieldFormDialog ,
				{
					formId: this.$parent.formID,
					fieldId: this.stepField.id,
					fieldTitle: '',
					fieldDesc: '',
					/**
					 * this method will be called in modal component to save data
					 */
					saveStep: async (StepData) => {
						this.$parent.selectedFields.push({
							id: this.stepField.id,
							name: this.stepField.name,
							content: StepData,
							type: 'step'
						});

						await this.saveParentFields()

						this.saveStepField()
					}
				},
				{
					height: '400px',
				}
			);
		},
		saveStepField(){
			const stepComponent = this.getNewlyAddedComponent( 'step' )

			if( stepComponent ){
				stepComponent.saveField()
			}
		}
	},
	/**
	 * Created hook to initialize the component data
	 */
	async created(){
		await this.loadFieldData();

		const availableFields = this.$parent.$children.find( component => component.$el.classList.contains( 'available-fields-holder' ) );
		if( availableFields ){
			availableFields.$on(  'add', () => {
				const index = this.$parent.availableFields.findIndex( field => field.id == this.id );
				if( index > -1 ){
					const el = document.querySelectorAll('.available-fields-holder > .widget')[index];
					if( el ){
						el.classList.add('destroyable');
						setTimeout(
							() => {
								el.remove();
								this.$parent.availableFields.splice(index, 1);
							},
							550
						);
					}
				}
			});
		}
	},
	watch: {
		/**
		 * Watches each action on selected fields list
		 * so we can render component based on field type
		 */
		selectedFields: {
			deep: true,
			async handler(){
				this.$parent.selectedFields.forEach((field, index) => {
					const widget = document.querySelectorAll(".droppable-fields > .widget")[index];

					if(!widget) return;

					if(!['html_content', 'step'].includes( field.type )){
						this.renderWidgetTop(widget, field);
					}
				});
			}
		}
	}
}
</script>
