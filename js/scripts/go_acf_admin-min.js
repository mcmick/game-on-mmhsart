jQuery(document).ready((function(){go_acf_repeater_accordion(),acf.addAction("new_field",(function(e){console.log("new field"),go_acf_repeater_accordion()})),go_hide_child_tax_acfs(),jQuery(".taxonomy-task_chains #parent, .taxonomy-go_badges #parent, .taxonomy-user_go_groups #parent").change((function(){go_hide_child_tax_acfs()})),jQuery('.acf-th[data-name="uniqueid"]').hide(),jQuery('.acf-field[data-name="uniqueid"]').hide()}));