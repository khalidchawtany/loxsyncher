	function getEditor(target, field){
		var opts = $(target).edatagrid('options');
		var t;
		var editor = $(target).datagrid('getEditor', {index:opts.editIndex,field:field});
		if (editor){
			t = editor.target;
		} else {
			var editors = $(target).datagrid('getEditors', opts.editIndex);
			if (editors.length){
				t = editors[0].target;
			}
		}
    return t;
	}

//date box formatter
function dateFormatterServer(date){
    var y = date.getFullYear();
    var m = date.getMonth()+1;
    var d = date.getDate();
  return y + '-'
    + (m<10?('0'+m):m) + '-'
    + (d<10?('0'+d):d);
}

function dateFormatter(date){
    var y = date.getFullYear();
    var m = date.getMonth()+1;
    var d = date.getDate();
    return (d<10?('0'+d):d) + '/'
        + (m<10?('0'+m):m) + '/'
        +y;
}

function dateParser(date){
    if (!date) return new Date();
    var date = (date.split('/'));
    var d = parseInt(date[0],10);
    var m = parseInt(date[1],10);
    var y = parseInt(date[2],10);

    if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
        return new Date(y,m-1,d);
    } else {
        return new Date();
    }
}

function dateParserServer(date){
    if (!date) return new Date();

    var date = (date.split('-'));
    var d = parseInt(date[2],10);
    var m = parseInt(date[1],10);
    var y = parseInt(date[0],10);

    if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
        return new Date(y,m-1,d);
    } else {
        return new Date();
    }
}

function dateTimeFormatter(dateTime){
    var y = dateTime.getFullYear();
    var m = dateTime.getMonth()+1;
    var d = dateTime.getDate();
    var hr=dateTime.getHours();
    var min=dateTime.getMinutes();
    var sec=dateTime.getSeconds();
    return y+'-'
        + (m<10?('0'+m):m) + '-'
        + (d<10?('0'+d):d) + ' '
        + (hr<10?('0'+hr):hr) + ':'
        + (min<10?('0'+min):min) + ':'
        + (sec<10?('0'+sec):sec);
}

function dateTimeParser(dateTime){
    if (!dateTime) return new Date();
    dateTime = dateTime.toString();
    var dateTimeArray = (dateTime.split(' '));
    var date = dateTimeArray[0];
    var time = dateTimeArray[1];
    var date = (dateTime.split('-'));
    var y = parseInt(date[0],10);
    var m = parseInt(date[1],10);
    var d = parseInt(date[2],10);

    var time = (time.split(':'));
    var hr = parseInt(time[0],10);
    var min = parseInt(time[1],10);
    var sec = parseInt(time[2],10);

    if (!isNaN(y) && !isNaN(m) && !isNaN(d)
        && !isNaN(hr) && !isNaN(min) && !isNaN(sec) ){
        return new Date(y,m-1,d, hr, min, sec);
    } else {
        return new Date();
    }
}

var showMessagerFunction = $.messager.show
$.messager.show = function(obj) {
  if (obj.title && obj.title == 'Error') {
    obj.cls = 'isErrorMessage'
  }
  showMessagerFunction(obj)
}

$.fn.datebox.defaults.formatter = dateFormatter;

$.fn.datebox.defaults.formatter = dateFormatter;
$.fn.datebox.defaults.formatterServer = dateFormatterServer;

$.fn.datebox.defaults.parser = dateParser;
$.fn.datebox.defaults.parserServer = dateParserServer;

$.fn.datetimebox.defaults.formatter = dateTimeFormatter;
$.fn.datetimebox.defaults.parser = dateTimeParser;

//Expand the combobox panle on focus
$.fn.combobox.defaults.showPanelOnFocus = true;
$.extend($.fn.combobox.defaults.inputEvents, {
    focus: function(e){
        var target = this;
    var showPanelOnFocus = $(e.data.target).combobox('options').showPanelOnFocus;
    if(showPanelOnFocus)
      $(e.data.target).combobox('showPanel');
    }
});

$.fn.datagrid.defaults = $.extend({}, $.fn.datagrid.defaults, {
  pageSize: 10,
    pageList: [10,15,20,25,30,40,50]

});

$.extend($.fn.datagrid.defaults, {
  loader: function(param, success, error){
      var opts = $(this).datagrid('options');
      if (!opts.url) return false;
      $.ajax({
        type: opts.method,
        url: opts.url,
        data: param,
        dataType: 'json',
        success: function(data){
          if (data.isError){
            $.messager.show({title: 'Error', msg: data.msg});
            error(data);
          } else {
            success(data);
          }
        },
        error: function(){
          error.apply(this, arguments);
        }
      });
    }
});

  $.extend($.fn.edatagrid.defaults, {
    onError: function(index, errorRow){

        var errorMessage = (errorRow.jqXHR)?
            errorRow.jqXHR.responseJSON.msg :
            errorRow.msg;

        showEdatagridTypingError(errorMessage);

        bindEdatagridKeypress(this, index);

        $(this).edatagrid('editRow', index);
    },

      onSave: function(index, row){
          if(row.obj && row.obj.id){
              $(this).datagrid('updateRow',{
                  index: index,
                  row: {id: row.obj.id}
              });
          }
          // if(!row.updated_at && row.obj.updated_at === row.obj.created_at)
          if(row.isNew){
              row.isNew = false;
              $(this).edatagrid('addRow');
          }

          var dgOptions = $(this).edatagrid('options');
          if(dgOptions.onSaveCallback) {
              dgOptions.onSaveCallback(index, row);
          }
      },

      onSuccess:function(index,row){ //This handles adding rew row when EDDITING a transaction
          $.messager.show({title: 'Success', msg: row.success});
          if(row.obj && row.obj.id){
              $(this).datagrid('updateRow',{
                  index: index,
                  row: {id: row.obj.id}
              });
          }
          if(row.isNew){
              row.isNew = false;
              $(this).edatagrid('addRow');
          }
      },

      onAdd:function(index, row){
          focusEditorIndex($(this), 0);
      },

      onBeforeEdit:function(index,row){
          row.isNew = row.isNewRecord;
          row.editing = true;
          $(this).edatagrid('refreshRow', index);
      },

      onAfterEdit:function(index,row){
          row.editing = false;
          $(this).edatagrid('refreshRow', index);
      },

      onCancelEdit:function(index,row){
          row.editing = false;
          $(this).edatagrid('refreshRow', index);
      },
  });

//
// Edatagrid
//
// On save fail , return the error message
function showEdatagridTypingError(message) {
    $.messager.show({
        title: 'Error',
        msg: message,
        timeout: 4000,
        showType: 'slide'
    });
}

/*
  bind the enter key to the target editors,
  they would be able to save the row on pressing enter
*/

function bindEdatagridKeypress(edatagrid, index) {

    // get the current row editors
    var editors = $(edatagrid).edatagrid('getEditors', index);

    // set focus on the first field
    var focused = editors[0];

    if(focused) {
        if (focused.type === 'validatebox') {
            $(focused.target).focus();
        } else {
            $(focused.target).textbox('textbox').focus();
        }
    }


    // bind the enter key press event to the current row editors
    $.each(editors, function(i, ed) {
        $(ed.target).keypress(function(event) {
            if (event.keyCode == 13) {
                $(edatagrid).edatagrid('saveRow');
            }
        });
    });

}

// Jeasyui edatagrid crud functions

function editRow(datagridId, index) {
    $('#' + datagridId).edatagrid('beginEdit', index);
}
function deleteRow(datagridId, index) {
    $('#' + datagridId).edatagrid('destroyRow', index);
}
function saveRow(datagridId, index) {
    $('#' + datagridId).edatagrid('endEdit', index);
}
function cancelRow(datagridId, index) {
    $('#' + datagridId).edatagrid('cancelEdit', index);
}


// Add datagrid dateBox filter range
$.extend($.fn.datagrid.defaults.filters, {
    dateRange: {
        init: function(container, options){
            var c = $('<div style="display:inline-block"><input class="d1"><input class="d2"></div>').appendTo(container);
            c.find('.d1,.d2').datebox();
            return c;
        },
        destroy: function(target){
            $(target).find('.d1,.d2').datebox('destroy');
        },
        getValue: function(target){
            var d1 = $(target).find('.d1');
            var d2 = $(target).find('.d2');
            return d1.datebox('getValue') + ','+d2.datebox('getValue');
        },
        setValue: function(target, value){
            var d1 = $(target).find('.d1');
            var d2 = $(target).find('.d2');
            var vv = value.split(',');
            d1.datebox('setValue', vv[0]);
            d2.datebox('setValue', vv[1]);
        },
        resize: function(target, width){
            $(target)._outerWidth(width)._outerHeight(22);
            $(target).find('.d1,.d2').datebox('resize', width/2);
        }
    }
});

// Add datagrid dateTimeBox filter range
$.extend($.fn.datagrid.defaults.filters, {
    dateTimeRange: {
        init: function(container, options){
            var c = $('<div style="display:inline-block"><input class="d1"><input class="d2"></div>').appendTo(container);
            c.find('.d1,.d2').datetimebox();
            return c;
        },
        destroy: function(target){
            $(target).find('.d1,.d2').datetimebox('destroy');
        },
        getValue: function(target){
            var d1 = $(target).find('.d1');
            var d2 = $(target).find('.d2');
            return d1.datetimebox('getValue') + ','+d2.datetimebox('getValue');
        },
        setValue: function(target, value){
            var d1 = $(target).find('.d1');
            var d2 = $(target).find('.d2');
            var vv = value.split(',');
            d1.datetimebox('setValue', vv[0]);
            d2.datetimebox('setValue', vv[1]);
        },
        resize: function(target, width){
            $(target)._outerWidth(width)._outerHeight(22);
            $(target).find('.d1,.d2').datetimebox('resize', width/2);
        }
    }
});


// Add dateBetween filter
$.extend($.fn.datagrid.defaults.operators, {
    dateBetween: {
        text: 'DateBetween',
        isMatch: function(source, value){
            var d1 = $.fn.datebox.defaults.parser(source);
            var d2 = $.fn.datebox.defaults.parser(value);
            return d1 > d2;
        }
    }
})

// Add dateTimeBetween filter
$.extend($.fn.datagrid.defaults.operators, {
    dateTimeBetween: {
        text: 'DateTimeBetween',
        isMatch: function(source, value){
            var d1 = $.fn.datetimebox.defaults.parser(source);
            var d2 = $.fn.datetimebox.defaults.parser(value);
            return d1 > d2;
        }
    }
})

$.extend($.fn.datagrid.defaults.operators, {
    dateGreater: {
        text: 'DateGreater',
        isMatch: function(source, value){
            var d1 = $.fn.datebox.defaults.parser(source);
            var d2 = $.fn.datebox.defaults.parser(value);
            return d1 > d2;
        }
    }
})

// Pressing Enter on textboxes moves focus to next input
$.extend($.fn.textbox.defaults.inputEvents, {
    keydown: function(e){
        if (e.keyCode == 13){
            var t = $(e.data.target);
            t.textbox('setValue', t.textbox('getText'));
            var onEnterPress = t.textbox('options').onEnterPress;
            if (onEnterPress) {
                e.preventDefault()
                onEnterPress(t, e);
                return;
            }

            var all = $('input:visible').not(':disabled').toArray().sort(function(a,b){
                var t1 = parseInt($(a).attr('tabindex')||1);
                var t2 = parseInt($(b).attr('tabindex')||1);
                return t1==t2?0:(t1>t2?1:-1);
            });
            var index = $.inArray(this, all);
            var nextIndex = (index+1) % all.length;
            $(all[nextIndex]).focus();
        }
    }
})


$.extend($.fn.numberbox.defaults.inputEvents, {
    keypress: function(e){
        if (e.keyCode == 13) {
            var t = $(e.data.target);
            t.numberbox('setValue', t.numberbox('getValue'));
            var onEnterPress = t.numberbox('options').onEnterPress;
            if (onEnterPress) {
                e.preventDefault()
                onEnterPress(t, e);
                return;
            }
        }
    }
})

// var originalDateboxEnterHandler  = $.fn.datebox.defaults.inputEvents.keydown;
// $.extend($.fn.datebox.defaults.inputEvents, {
//     keydown: function(e) {
//         var result = originalDateboxEnterHandler.call(this, e);
//         if (e.keyCode == 13) {
//             var onEnterPress = $(e.data.target).datebox('options').onEnterPress;
//             if (onEnterPress) {
//                 onEnterPress(this, e);
//             }
//         }
//       return result;
//     }
// });


var originalComboboxEnterHandler  = $.fn.combobox.defaults.keyHandler.enter;
$.extend($.fn.combobox.defaults.keyHandler, {
    enter: function(e) {
        originalComboboxEnterHandler.call(this, e);
        // $.fn.combobox.defaults.keyHandler.enter.call(this,e);
        var onEnterPress = $(this).combobox('options').onEnterPress;
        if (onEnterPress) {
            onEnterPress(this, e);
        }
    }
});

var originalCombogridEnterHandler  = $.fn.combogrid.defaults.keyHandler.enter;
$.extend($.fn.combogrid.defaults.keyHandler, {
    enter: function(e) {
      var val = $(e.currentTarget).val();
      originalCombogridEnterHandler.call(this, e);
      if (val == undefined || val.length == 0) {
        $(this).combogrid('clear');
      } else {
        cbgg = $(this).combogrid('grid');
        var selected = cbgg.datagrid('getSelected')
        if (selected == null ) {
          var rows = cbgg.datagrid('getRows');
          if (rows.length > 0) {
            $(this).combogrid('setValue', rows[0]);
          }
        }
      }
        // $.fn.combogrid.defaults.keyHandler.enter.call(this,e);
        var onEnterPress = $(this).combogrid('options').onEnterPress;
        if (onEnterPress) {
            onEnterPress(this, e);
        }
    }
});


////Bind onEnterPress to validatebox
//var originalValidateboxFocusHandler  = $.fn.validatebox.defaults.events.focus;
//var originalValidateboxBlurHandler  = $.fn.validatebox.defaults.events.blur;
//$.extend($.fn.validatebox.defaults.events, {
//  enter: function(e){
//        if (e.keyCode == 13) {
//          var onEnterPress = $(this).validatebox('options').onEnterPress;
//          if (onEnterPress) {
//            onEnterPress($(this).validatebox(), e);
//          }
//        }
//  },
//  focus: function(e) {
//    originalValidateboxFocusHandler.call(this, e);
//    var onEnterPress = $(e.data.target).validatebox('options').onEnterPress;
//    if (onEnterPress) {
//      var target = $(e.target);
//      target.bind('keydown', $.fn.validatebox.defaults.events.enter);
//    }
//  },
//  blur: function(e) {
//    originalValidateboxBlurHandler.call(this, e);
//    var onEnterPress = $(e.data.target).validatebox('options').onEnterPress;
//    if (onEnterPress) {
//      var target = $(e.target);
//      target.unbind('keydown');
//    }
//  }
//});




function init(target){
  var widths = [320,480,640,800,960,1120];
  var dg = $(target);
  var opts = dg.datagrid('options');
  var onResize = dg.datagrid('getPanel').panel('options').onResize;
  dg.datagrid('getPanel').panel('options').onResize = function(width,height){
    var width = $(this).panel('panel').outerWidth();
    var priority = getPriority(width);
    var fields = dg.datagrid('getColumnFields',true).concat(dg.datagrid('getColumnFields'));
    for(var i=0; i<fields.length; i++){
      var col = dg.datagrid('getColumnOption', fields[i]);
      if (col.priority && col.priority>priority){
        dg.datagrid('hideColumn', fields[i]);
      } else {
        dg.datagrid('showColumn', fields[i]);
      }
    }
    onResize.call(this, width, height);
  }

  function getPriority(width){
    for(var i=0; i<widths.length; i++){
      if (width<widths[i]){
        return i;
      }
    }
    return widths.length;
  }
}

$.extend($.fn.datagrid.methods, {
  columnToggle: function(jq){
    return jq.each(function(){
      init(this);
      $(this).datagrid('resize');
    });
  }
});
