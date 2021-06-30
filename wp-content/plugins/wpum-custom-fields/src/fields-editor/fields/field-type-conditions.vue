<template>
	<div class="conditions-wrapper">
		<div class="spinner is-active" v-if="loading"></div>
		<div class="conditions-group" v-else v-for="(rules,groupKey) in ruleGroups" :key="groupKey">
			<template v-if="rules.length">
				<label>{{ groupKey > 0 ? translations.or : translations.show_field_if }}</label>
				<div class="conditions-row" v-for="(rule,ruleKey) in rules" :key="ruleKey">
					<select v-model="rule.field">
						<option v-for="(field,index) in availableFields" :key="index" :value="field.value">{{field.label}}</option>
					</select>
					<select v-model="rule.condition">
						<option v-for="(condition,index) in availableConditions" :key="index" :value="condition.value">{{condition.label}}</option>
					</select>
					<input type="text" v-model="rule.value" :disabled="rule.condition == 'has_value' || rule.condition == 'has_no_value'">
					<button class="button" @click="addGroupRule(groupKey)">{{translations.and}}</button>
					<button class="button remove" @click="removeGroupRule(groupKey, ruleKey)" v-if="groupKey > 0 || ruleKey > 0">
						<span class="dashicons dashicons-remove"></span>
					</button>
				</div>
			</template>
		</div>
		<div class="condition-or-group" v-if="!loading">
			<div><strong>{{translations.or}}</strong></div>
			<button class="button add-group" @click="addGroup">{{translations.add_rule_group}}</button>
		</div>
	</div>
</template>
<script>

import VueFormGenerator from 'vue-form-generator'
import axios from 'axios'
import qs from 'qs'

export default {
	name: 'field-conditional',
	mixins: [ VueFormGenerator.abstractField ],
	components: {

	},
	data(){
		return {
			loading: false,
			enabled: false,
			ruleGroups: [],
			conditions: window.wpumcfFieldEditor.conditions,
			translations: window.wpumcfFieldEditor.labels,
			fields: []
		}
	},
	computed: {
		availableFields(){
			const fieldId = this.vfg.$parent.field_id;
			return this.fields.filter(field => field.id != fieldId).map(field => ({ value: field.id, label: field.name }) );
		},
		availableConditions(){
			return Object.keys(this.conditions).map((condition) => {
				return {
					value : condition,
					label: this.conditions[condition]
				}
			});
		}
	},
	methods: {
		addGroupRule(group){
			this.ruleGroups[group].push({
				field: '',
				condition: '',
				value: ''
			})
		},
		addGroup(){
			this.ruleGroups.push([
				{
					field: '',
					condition: '',
					value: ''
				}
			]);
		},
		removeGroupRule(group, rule){
			if( this.ruleGroups[group] && this.ruleGroups[group][rule] && ( this.ruleGroups.length > 1 || this.ruleGroups[group].length > 1 ) ){
				this.ruleGroups[group].splice(rule, 1);

				if( !this.ruleGroups[group].length ){
					this.ruleGroups.splice(group, 1);
				}
			}
		},
		loadConditionalFields(){

			const data 	  =  Object.assign( {}, this.$route );
			const fieldId = this.vfg.$parent.field_id;

			delete data['matched'];

			this.loading = true;

			axios.post(
				ajaxurl,
				qs.stringify({
					nonce:     wpumFieldsEditor.get_fields_nonce,
					field_id : fieldId
				}),
				{
					params: {
						action: 'wpumcf_get_conditional_fields'
					}
				}
			)
			.then((response) => {

				if( response.data.data && Array.isArray( response.data.data ) ){
					this.fields = response.data.data;
				}

				this.loading = false;
			})
			.catch(() => {
				this.loading = false;
			});
		}
	},
	mounted(){

		this.loadConditionalFields();

		this.ruleGroups = this.model.conditions;

		if( this.ruleGroups.length < 1 ){
			this.ruleGroups.push([
				{
					field: '',
					condition: '',
					value: ''
				}
			]);
		}

		this.$options.watch.model.handler.call(this, this.model);
	},
	watch: {
		model: {
			deep: true,
			handler( model ){
				this.$el.closest('.form-group').style.display = model.enable_condition ? '' : 'none';
			}
		},
		ruleGroups: {
			deep: true,
			handler( ruleGroups ){
				this.model.conditions = ruleGroups;
			}
		}
	}
}
</script>

<style scoped>
	.conditions-row {
		display: flex;
		flex-direction: row;
	}
	.conditions-row > *:not(:last-child) {
		margin-right: 5px;
	}
	.conditions-row + .conditions-row {
    	margin-top: 6px;
	}
	.conditions-group + .conditions-group {
    	margin-top: 6px;
	}
	.condition-or-group {
    	margin-top: 10px;
	}
	.conditions-row .remove {
		color: red;
		border-color: red;
		line-height: 1;
		outline: none !important;
	}
	.conditions-row select > option {
		display: initial !important;
	}
</style>