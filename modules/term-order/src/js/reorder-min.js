function setParams(){var e="?";for(key in params){var t=params[key];e+=key+"="+t+"&"}return console.log("string: "+e),e.slice(0,-1)}function toggle(e){for(var t=jQuery("input[name='delete_tags[]']"),r=0,a=t.length;r<a;r++)t[r].checked=e.checked;var n=e.checked;jQuery("#cb-select-all-1").prop("checked",n),jQuery("#cb-select-all-2").prop("checked",n)}jQuery.extend({replaceTag:function(e,t,r){var a=jQuery(e),n,i=jQuery(t).clone();return r&&(newTag=i[0],newTag.className=e.className,newTag.id=jQuery(e).attr("id"),jQuery.extend(newTag.classList,e.classList),jQuery.extend(newTag.attributes,e.attributes)),a.wrapAll(i),a.contents().unwrap(),this}}),jQuery.fn.extend({replaceTag:function(e,t){return this.each(function(){jQuery.replaceTag(this,e,t)})}});var params={};window.location.search.substr(1).split("&").forEach(function(e){params[e.split("=")[0]]=e.split("=")[1]});var filtered_terms=["store_types","task_chains","user_go_groups","go_badges"];if(filtered_terms.includes(params.taxonomy)){var taxonomy=params.taxonomy,parent=params.parent;if(parent){var parent_data='data-value="'+parent+'"',parentName=params.parentName;if(parentName){parentName=parentName.replace(/\+/g,"&nbsp;");var parentName_data='data-value_name="'+(parentName=decodeURIComponent(parentName))+'"'}}console.log(parentName),jQuery(document.querySelector(".tablenav .bulkactions")).after('<select id="go_page_'+taxonomy+'_select" '+parent_data+parentName_data+' class="go_activate_filter"></select> <button id="js-filter" class="button go_apply_filters" type="button">Filter</button> '),go_make_select2_filter(taxonomy,!1,!1,!0);var value=decodeURIComponent(params.meta_value).replace(/\+/g," ");jQuery("#js-filter-dropdown").find('option[value="'+value+'"]').prop("selected",!0),jQuery("#js-filter").click(function(){var e=jQuery("#go_page_"+taxonomy+"_select").val(),t=jQuery("#go_page_"+taxonomy+"_select option:selected").text();e?(params.parent=encodeURIComponent(e),params.parentName=encodeURIComponent(t)):(delete params.parent,delete params.parentName),window.location.search=setParams()})}jQuery("#cb-select-all-1").click(function(){toggle(this)}),jQuery("#cb-select-all-2").click(function(){toggle(this)}),jQuery(document).ready(function(){var e=[];jQuery("#the-list").find("tr").each(function(){e.push(this.id)}),termDivIDs=e,console.log("termDivIDs"),console.log(termDivIDs),jQuery.ajax({type:"POST",url:ajax_url,data:{action:"check_if_parent_term",goTermDivIDs:termDivIDs},success:function(t){function r(e){var t=jQuery.parseJSON(e),a=t.new_pos;for(var n in a)if("next"!==n){var i=document.getElementById("inline_"+n);if(null!==i&&a.hasOwnProperty(n)){var o=i.querySelector(".order");if(void 0!==a[n].order){null!==o&&(o.innerHTML=a[n].order);var d=i.querySelector(".parent");null!==d&&(d.innerHTML=a[n].parent);var c=null,u=i.querySelector(".row-title");null!==u&&(c=u.innerHTML);for(var p=0;p<a[n].depth;)p++;var h=i.parentNode.querySelector(".row-title")}else null!==o&&(o.innerHTML=a[n])}}t.next?jQuery.post(ajaxurl,{action:"reordering_terms",id:t.next.id,previd:t.next.previd,nextid:t.next.nextid,start:t.next.start,excluded:t.next.excluded,tax:l},r):(setTimeout(function(){jQuery(".to-row-updating").removeClass("to-row-updating")},500),s.removeClass("to-updating").sortable("enable"))}for(var a=jQuery.parseJSON(t),n=0;n<e.length;n++){var t=a[n],i="#"+e[n];jQuery(i).addClass(t)}jQuery("#the-list").find(".parent").each(function(e){jQuery(this).wrapInner('<div class="container parent"></div>');var t=jQuery(this).attr("id");jQuery(this).nextUntil(".parent").andSelf().wrapAll("<li id="+t+' class="sortset"></li>');var t=jQuery(this).attr("id");jQuery(this).nextUntil(".parent").wrapAll("<ul id="+t+' class="children ulSortable"></ul>'),jQuery(this).contents().unwrap()}),jQuery("#the-list").find(".child").each(function(e){jQuery(this).wrapInner('<div class="container child"></div>')}),jQuery("tbody#the-list").replaceTag("<ul>",!0),jQuery("#the-list").addClass("ulSortable",!0),jQuery("#the-list").find("tr").replaceTag("<li>",!0),jQuery("#the-list").find("td").replaceTag("<div>",!0),jQuery(".container").prepend('<div class="handleLeft"><i class="fa fa-arrows-v fa-1x" aria-hidden="true"></i></div>'),jQuery(".container.parent").append('<div class="handleRight"><i class="fa fa-chevron-up fa-1x" aria-hidden="true"></i></i></div>'),jQuery(".container.child").append('<div class="handleRight_nograb"></div>'),jQuery(".handleRight").mousedown(function(){jQuery(".child.ui-sortable-handle").css("display","none"),jQuery("body").mouseup(function(){jQuery(".child.ui-sortable-handle").css("display","block")})});var s=jQuery(".ulSortable"),l=jQuery('form input[name="taxonomy"]').val();s.sortable({items:"> li",cursor:"move",axis:"y",cancel:"  .inline-edit-row",distance:2,opacity:.9,tolerance:"pointer",scroll:!0,nested:"ul",containment:"parent",forceHelperSize:!0,forcePlaceholderSize:!0,cursorAt:{top:25,left:15},start:function(e,t){"undefined"!=typeof inlineEditTax&&inlineEditTax.revert(),t.placeholder.height(t.item.height()),t.item.parent().parent().addClass("dragging")},helper:function(e,t){return t.children().each(function(){jQuery(this).width(jQuery(this).width())}),t},update:function(e,t){s.sortable("disable").addClass("to-updating"),t.item.addClass("to-row-updating");var a=4,n=t.item[0].id.substr(4),i=!1,o=t.item.prev();o.length>0&&(i=o.attr("id").substr(4));var d=!1,c=t.item.next();c.length>0&&(d=c.attr("id").substr(4)),jQuery.post(ajaxurl,{action:"reordering_terms",id:n,previd:i,nextid:d,tax:l},r)}}),jQuery("thead li").each(function(e){jQuery(this).wrapInner('<div class="container term_list_header"></div>')}),jQuery("tfoot li").each(function(e){jQuery(this).wrapInner('<div class="container term_list_footer"></div>')}),jQuery("thead").wrapInner('<div class="the-list-header"></div>'),jQuery("thead").contents().unwrap(),jQuery("th").replaceTag("<div>",!0),jQuery("tfoot").wrapInner('<div class="the-list-footer"></ul>'),jQuery("tfoot").contents().unwrap(),jQuery(".the-list-footer tr").prepend('<div class="handleLeft"></div>'),jQuery(".the-list-header tr").prepend('<div class="handleLeft"></div>'),jQuery(".the-list-footer tr").append('<div class="handleRight_nograb"></div>'),jQuery(".the-list-header tr").append('<div class="handleRight_nograb"></div>'),jQuery(".the-list-header tr").replaceTag("<div class=container>",!0),jQuery(".the-list-header td").replaceTag("<div>",!0),jQuery(".the-list-footer tr").replaceTag("<div class=container>",!0),jQuery(".the-list-footer td").replaceTag("<div>",!0),jQuery("#col-right").css("display","block");var o=jQuery("#posts").width(),d=jQuery("#pod_toggle").width(),c=jQuery("#pod_done_num").width(),u=jQuery("#pod_achievement").width(),p=jQuery("#description").width(),h=jQuery("#slug").width();jQuery("<style>.posts { width: "+o+"px; }.pod_toggle { width: "+d+"px; }.pod_done_num { width: "+c+"px; }.pod_achievement { width: "+u+"px; }.description { width: "+p+"px; }.slug { width: "+h+"px; }</style>").appendTo("head"),jQuery(".posts").css("width",o+"px"),jQuery(".pod_toggle").css("width",d+"px"),jQuery(".pod_done_num").css("width",c+"px"),jQuery(".pod_achievement").css("width",u+"px"),jQuery(".description").css("width",p+"px"),jQuery(".slug").css("width",h+"px"),jQuery("#submit").click(function(){jQuery("#col-right").css("display","none"),jQuery("#col-right").bind("DOMSubtreeModified",function(){console.log("modified and reload"),location.reload(!0)}),1==jQuery(".term-name-wrap").hasClass("form-invalid")&&(jQuery("#col-right").unbind("DOMSubtreeModified"),jQuery("#col-right").css("display","block")),1==jQuery(".formfield").hasClass("form-invalid")&&jQuery(".formfield").css("display","block")})},error:function(e){console.log(e),console.log("fail")}})});