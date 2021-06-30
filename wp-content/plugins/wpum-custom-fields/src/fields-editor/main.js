import ConditionalField from './fields/field-type-conditions.vue';

window.addEventListener("wpum-api-ready", function(e){
    // const hooks = e.detail.Hooks;
	const Vue = e.detail.Vue;

    Vue.component('field-conditional', ConditionalField);
});