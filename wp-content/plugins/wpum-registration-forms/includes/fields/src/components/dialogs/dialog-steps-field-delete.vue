<template>
	<div class="media-modal-content wpum-dialog" id="html-field-delete-dialog">
		<button type="button" class="media-modal-close" @click="$emit('close')"><span class="media-modal-icon"><span class="screen-reader-text">Close panel</span></span></button>
		<div class="media-frame mode-select wp-core-ui">
			<div class="media-frame-title">
				<h1>Delete Step Field</h1>
			</div>
			<div class="media-frame-content">
				<p>Are you sure want to delete this field? This cannot be undone.</p>
			</div>
			<div class="media-frame-toolbar">
				<div class="media-toolbar">
					<div class="media-toolbar-primary">
						<div class="spinner is-active" v-if="loading"></div>
						<button style="min-width:100px;" type="button" class="button media-button button-large" :disabled="loading" @click="$emit('close')">Cancel</button>
						<button style="min-width:100px;" type="button" class="button media-button button-danger button-large media-button-insert" :disabled="loading" @click="deleteField">Delete</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import Axios from 'axios'

export default {
	name: 'steps-field-delete-dialog',
	props: {
		deleteStepsField: '',
	},
    data() {
		return {
			loading: false,
		}
	},
	methods: {
		/**
		 * Calls the callback to delete the field
		 */
		async deleteField(){
			this.loading = true;

			await this.deleteStepsField();

			this.loading = false;
			this.$emit('close');
		}
	}
}
</script>
