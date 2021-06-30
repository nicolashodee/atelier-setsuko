import formActions from "./components/formActions.vue"
import FieldTypes from './components/field-types';

window.addEventListener("wpum-api-ready", function(e){
    const hooks = e.detail.Hooks;
	const Vue = e.detail.Vue;

	FieldTypes.forEach( ( Field ) => { Vue.component( Field.name, Field ) } )

    Vue.component('form-actions', formActions);

    hooks.addFilter("droppableFieldAfter", "wpumrf", function(components){
        components.push(formActions);
        return components;
    }, 1);

});
