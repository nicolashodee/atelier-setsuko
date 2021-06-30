const { assign } = lodash;

const { createHigherOrderComponent } = wp.compose;
const { Fragment } = wp.element;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, SelectControl, ToggleControl } = wp.components;
const { addFilter } = wp.hooks;
const { __ } = wp.i18n;

// Disable restriction controls on the following blocks...
// * Add to the array... i.e ["core/paragraph", "core/image"]
const enableRestrictionControlsOnTheseBlocks = ["wpum/registration-form"];

const formOptions = [];
wp.apiFetch({ path: "/wp-user-manager/registration-forms" }).then(forms =>
	forms.map(function(form) {
		formOptions.push({ value: form.value, label: form.label });
	})
);

/**
 * Add restriction control attributes to block.
 *
 * @param {object} settings Current block settings.
 * @param {string} name Name of block.
 *
 * @returns {object} Modified block settings.
 */
// const addRegistrationFormPickerControlAttributes = (settings, name) => {
// 	// Do nothing if it's another block than our defined ones.
// 	if (!enableRestrictionControlsOnTheseBlocks.includes(name)) {
// 		return settings;
// 	}

// 	// Use Lodash's assign to gracefully handle if attributes are undefined
// 	settings.attributes = assign(settings.attributes, {
// 		form_id: {
// 			type: "string",
// 			default: "1"
// 		}
// 	});
// 	return settings;
// };

// addFilter(
// 	"blocks.registerBlockType",
// 	"wp-user-manager/attribute/registration-form-picker",
// 	addRegistrationFormPickerControlAttributes
// );

/**
 * Create HOC to add restriction controls to inspector controls of block.
 */
const withRegistrationFormPickerControls = createHigherOrderComponent(
	BlockEdit => {
		return props => {
			// Do nothing if it's another block than our defined ones.
			if (!enableRestrictionControlsOnTheseBlocks.includes(props.name)) {
				return <BlockEdit {...props} />;
			}

			const { form_id } = props.attributes;

			return (
				<Fragment>
					<BlockEdit {...props} />
					<InspectorControls>
						<PanelBody
							title={__(
								"Select Registration Form"
							)}
							initialOpen={true}
						>
							<SelectControl
								value={form_id}
								options={formOptions}
								onChange={selectedForm => {
									props.setAttributes({
										form_id: selectedForm
									});
								}}
							/>
						</PanelBody>
					</InspectorControls>
				</Fragment>
			);
		};
	},
	"withRegistrationFormPickerControls"
);

addFilter(
	"editor.BlockEdit",
	"wp-user-manager/with-registration-form-controls",
	withRegistrationFormPickerControls
);
