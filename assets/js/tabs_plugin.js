(function() {
    tinymce.create('tinymce.plugins.oscitasRestabs', {
        init : function(ed, url) {
            ed.addButton('oscitasrestabs', {
                title : 'Tabs Shortcode',
                image : url+'/icon.png',
                onclick : function() {
                    ert_create_oscitas_responsive_tab();
                    //console.log('sds');
                    jQuery( "#ert-form-restabs" ).dialog({
                        dialogClass : 'wp-dialog',
                        autoOpen: true,
                        height: 'auto',
                        width: 800,
                        modal: true
                    });
                    //console.log('sds');

//                    jQuery.fancybox({
//                        'autoSize':false,
//                        'autoWidth':false,
//                        'fitToView':false,
//                        'height':'auto',
//                        'topRatio':0.1,
//                        'width':800,
//                        'type' : 'inline',
//                        'title' : 'Responsive Tab Shortcode',
//                        'href' : '#ert-form-restabs',
//                        helpers:  {
//                            title : {
//                                type : 'over',
//                                position:'top'
//                            }
//                        }
//                    });
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
        getInfo : function() {
            return {
                longname : "Responsive Tabs Shortcode",
                author : 'Oscitas Themes',
                authorurl : 'http://www.oscitasthemes.com/',
                infourl : 'http://www.oscitasthemes.com/',
                version : "1.0"
            };
        }
    });
    tinymce.PluginManager.add('oscitasrestabs', tinymce.plugins.oscitasRestabs);
})();
function ert_create_oscitas_responsive_tab(){
    if(jQuery('#ert-form-restabs').length){
        jQuery('#ert-form-restabs').remove();
    }
    // creates a form to be displayed everytime the button is clicked
    // you should achieve this using AJAX instead of direct html code like this
    var form = jQuery('<div id="ert-form-restabs" title="Easy Responsive Tab Shortcode"><table id="oscitas-table" class="form-table" style="margin-top: 0px;">\
			<tr>\
				<th><label for="oscitas-restabs-position">Show Tabs Position</label></th>\
				<td><select name="type" id="oscitas-restabs-position">\
					<option value="">Top</option>\
					<option value="tabs-below">Bottom</option>\
				</select><br />\
				</td>\
			</tr>\
			<tr>\
				<th><label for="oscitas-restabs-pills">Tabs With Pills</label></th>\
				<td>\
				    <input type="checkbox" id="oscitas-restabs-pills">\
                    <small>Check this checkbox to show selector on selected tab</small>\
				</td>\
			</tr>\
			<tr>\
				<th><label for="oscitas-restabs-icon">Use Icon</label></th>\
				<td>\
				    <input type="checkbox" id="oscitas-restabs-icon">&nbsp;&nbsp;&nbsp;<i class="res_tab_icon"></i>\
				</td>\
			</tr>\
			<tr>\
				<th><label for="oscitas-restabs-text">Drop Down Text</label></th>\
				<td><input type="text" name="title" id="oscitas-restabs-text" value="More"/><br />\
				</td>\
			</tr>\
			<tr>\
				<th><label for="oscitas-restabs-number">Number of Tabs</label></th>\
				<td><input type="text" name="title" id="oscitas-restabs-number" value="4"/><br/>Enter a numeric value, Default is 4</td>\
			</tr>\
             <tr>\
				<th><label for="oscitas-restabs-class">Custom Class</label></th>\
				<td><input type="text" name="line" id="oscitas-restabs-class" value=""/><br />\
				</td>\
			</tr>\
		</table>\
		<p class="submit" style="padding-right: 10px;text-align: right;">\
			<input type="button" id="oscitas-restab-submit" class="button-primary" value="Insert Responsive Tabs" name="submit" />\
		</p>\
		</div>');
    var table = form.find('table');
    form.appendTo('body').hide();
    form.find('#oscitas-restab-submit').click(function(){

        var cusclass='',icon='',text='',pills='',position='',item= 0,eactive='';
        var num=table.find('#oscitas-restabs-number').val();
        if(jQuery('#oscitas-restabs-pills').prop('checked')){
            pills=' pills="nav-pills"';

        }
        if(jQuery('#oscitas-restabs-icon').prop('checked')){
            icon=' icon="true"';

        }
        if(table.find('#oscitas-restabs-text').val()!=''){
            text= ' text="'+table.find('#oscitas-restabs-text').val()+'"';
        }
        if(table.find('#oscitas-restabs-position').val()!=''){
            position= ' position="tabs-below"';
        }

        if(table.find('#oscitas-restabs-class').val()!=''){
            cusclass= ' class="'+table.find('#oscitas-restabs-class').val()+'"';
        }
        if( Math.floor(num) == num && jQuery.isNumeric(num)){
            item=num;
        } else{
            item=4;
        }
            var shortcode = '[restabs'+position+pills+icon+text+cusclass;
        shortcode += ']';

            for(var i=1;i<=item;i++){
                if(i==1){
                    eactive=' active="active"';
                }
                else{
                    eactive='';
                }
                shortcode+='<br/>[restab title="Tab number '+i+'"'+eactive+']Tab '+i+' content goes here.[/restab]';
            }
        shortcode += '[/restabs]';

        // inserts the shortcode into the active editor
        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

        // closes dialog box
        jQuery( "#ert-form-restabs" ).dialog('close');

    });
}


