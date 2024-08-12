function formatAttachments(attachments, row) {
  if (Array.isArray(attachments))
    if (attachments.length > 0)
      return attachments.map(function(attachment) {
        return '<a href="' + attachment + '" target="_blank ">Attachment</a>';
      });

  return attachments ? attachments : "---";
}

function getSelectedRow(id, rowType) {
    var row = $("#" + id).datagrid("getSelected");
    if (row) {
        return row;
    } else {
        $.messager.show({
            title: "Error",
            msg: "Please select a " + rowType
        });
    }
}

function removeResourceById(id, url, rowType, callback) {
    var row = getSelectedRow(id, rowType);

    if (!row) {
        return;
    }

    $.messager.confirm('Confirm', 'Are you sure you want to delete this ' + rowType, function (r) {
        if (r) {

            $.post(url, {
                'id': row.id,
            }, function (result) {
                if (result.success) {
                    callback();
                } else {
                    $.messager.show({ title: 'Error', msg: result.msg });
                }
            }, 'json');
        }
    });
}

function openDialogWithUrl(id, url, options = {}) {
    $("#" + id).dialog("open");

    url += url.indexOf('?') > -1 ? '&' : '?';

    url += 'dialogId=' + id;

    // if (typeof title !== "undefined") $("#" + id).dialog("setTitle", title);

    //{onClose: function(){ callback(); }}
    $("#" + id).dialog("refresh", url).dialog(options);
}

function onChangeDatagridFilterConrols(datagrid, filter, value, op) {
  if (value == "") {
    $("#" + datagrid).datagrid("removeFilterRule", filter);
  } else {
    $("#" + datagrid).datagrid("addFilterRule", {
      field: filter,
      value: value,
      op: typeof op !== "undefined" ? op : "like"
    });
  }
  $("#" + datagrid).datagrid("doFilter");
}

function datagridFilterByDate(holder, datagrid, field) {
  var filter_from =
    $("#" + holder + " #filter_from").datebox("getValue") || "01-01-2010";
  var filter_to =
    $("#" + holder + " #filter_to").datebox("getValue") || "01-01-2100";

  $("#" + datagrid).datagrid("addFilterRule", {
    field: field,
    filter_from: filter_from,
    filter_to: filter_to
  });

  $("#" + datagrid).datagrid("doFilter");
}

function clearDatagridFilterByDate(holder, datagrid, field) {
  $("#" + holder + " #filter_from").datebox("setValue", "");
  $("#" + holder + " #filter_to").datebox("setValue", "");
  onChangeDatagridFilterConrols(datagrid, field, "");
}

function loadPanel(container, url, onload) {
  if ($(container).children().length) return onload();
  $(container).panel({ href: url, onLoad: onload });
}

function resetDatagrid(dg) {
  if (dg.length > 0) {
    dg.datagrid("clearSelections");
    dg.datagrid("loadData", { total: 0, rows: [] });
  }
}

function getEdittingRow(dg) {
  dg = $(dg);
  var dgindex = getEdittingRowIndex(dg);
  var dgRow = dg.datagrid("getRows")[dgindex];
  return dgRow;
}

function getEdittingRowIndex(dg) {
  dg = $(dg);
  var dgindex = dg.datagrid("getRowIndex", dg.datagrid("getSelected"));
  return dgindex;
}

function focusEditorIndex(dg, index) {
  var field = dg.datagrid("getColumnFields")[index];
  $.fn.focusEditor(dg, field);
}

function isElectron() {
  var userAgent = navigator.userAgent.toLowerCase();
  return userAgent.indexOf(" electron/") > -1;
}

function print(url) {
  // $("<iframe>").hide().attr("src", url).appendTo("body");
  // return;
  if (isElectron()) {
    if (typeof url == "string") {
      url = { urls: url, paper: "a4" };
    }

    window.Bridge.printUrl(url);
  } else {
    $("<iframe>")
      .hide()
      .attr("src", url)
      .appendTo("body");
  }
}

function checkDuplicateInObject(propertyName, inputArray) {
  var seenDuplicate = false,
    testObject = {};

  inputArray.map(function(item) {
    var itemPropertyName = item[propertyName];
    if (itemPropertyName in testObject) {
      testObject[itemPropertyName].duplicate = true;
      item.duplicate = true;
      seenDuplicate = true;
    } else {
      testObject[itemPropertyName] = item;
      delete item.duplicate;
    }
  });

  return seenDuplicate;
}

function focusNextControl(fromControl, controls) {
  for (var i = 0; i < controls.length; i++) {
    var control = controls[i];
    if (fromControl === control.id) {
      break;
    }
  }

  if (i >= controls.length) {
    return;
  }

  var targetControl = controls[++i];

  // if target control is disabled then call me  recursively with the target
  // control
  if (targetControl.skip) {
    focusNextControl(targetControl.id, controls);
    return;
  }

  //Action
  if (targetControl.action) {
    eval(targetControl.action);
    return;
  }

  //Focus
  eval('$("#' + targetControl.id + '").' + targetControl.type + "focus();");
}

function handleShowTotalChange(checkbox, dg) {
  var checked = checkbox.checked;

  var dgOptions = $(dg).edatagrid("options");

  var pager = $(dg).datagrid("getPager");

  dgOptions.queryParams["hide_total_page_count"] = !checked;

  if (checked) {
    pager.pagination({
      displayMsg: "Displaying {from} to {to} of {total} items"
    });
  } else {
    pager.pagination({
      displayMsg: ""
    });
  }
}

function calculateAverageFor(
  fieldId,
  maxSamples,
  precision,
  prependIndex = false
) {
  var val = 0;
  var fieldsWithValue = 0;

  for (var i = 1; i <= maxSamples; i++) {
    var fieldVal;

    if (prependIndex) {
      fieldVal = +$("#" + fieldId + i).textbox("getValue");
    } else {
      fieldVal = +$("#s" + i + fieldId).textbox("getValue");
    }

    if (isNaN(fieldVal) || fieldVal == "") {
      continue;
    }

    fieldsWithValue++;

    val += fieldVal;
  }

  if (fieldsWithValue == 0) {
    return;
  }

  val = val / fieldsWithValue;
  val = +val.toFixed(precision);
  $("#" + fieldId + "_average").textbox("setValue", val);
  return val;
}

$.extend({}, $.fn.combobox.methods, {
  appendItem: function(jq, item) {
    return jq.each(function() {
      var state = $.data(this, "combobox");
      var opts = state.options;
      var items = $(this).combobox("getData");
      items.push(item);
      $(this)
        .combobox("panel")
        .append(
          '<div id="' +
          state.itemIdPrefix +
          "_" +
          (items.length - 1) +
          '"  class="combobox-item">' +
          (opts.formatter
            ? opts.formatter.call(this, item)
            : item[opts.textField]) +
          "</div>"
        );
    });
  }
});

function sendPrintCommandToServer(urls, paper, args) {
  if (!paper) {
    paper = "A4";
  }

  if (!args) {
    args = "";
  }

  if (isElectron()) {
    print({
      urls: urls,
      paper: paper
    });

    return;
  }

  $.ajax({
    url: "/print",
    data: {
      urls: urls,
      paper: paper,
      args: args
    },
    success: function(result) {
      if (result.isError) {
        $.messager.show({ title: "Error", msg: result.msg });
      } else {
        $.messager.show({ title: "Success", msg: result.success });
      }
    },
    dataType: "json"
  });
}

function diffFilter(obj1, obj2) {
  var result = {};
  for (key in obj1) {
    if (obj2[key] != obj1[key]) result[key] = obj2[key];
    if (typeof obj2[key] == "array" && typeof obj1[key] == "array")
      result[key] = arguments.callee(obj1[key], obj2[key]);
    if (typeof obj2[key] == "object" && typeof obj1[key] == "object")
      result[key] = arguments.callee(obj1[key], obj2[key]);
  }
  return result;
}

function ucfirst(str) {
  // checks for null, undefined and empty string
  if (!str) return;
  return str.match("^[a-z]")
    ? str.charAt(0).toUpperCase() + str.substring(1)
    : str;
}

function toggleDgColumn(el, dg) {
  var menu = $(el.parentNode).menu();
  var menuItem = menu.menu("getItem", el);
  var field = menuItem.field !== undefined ? menuItem.field : menuItem.name;

  if (menuItem.iconCls === "icon-mini-add") {
    $("#" + dg).datagrid("showColumn", field); // show the column

    menu.menu("setIcon", {
      target: menuItem.target,
      iconCls: ""
    });
  } else {
    $("#" + dg).datagrid("hideColumn", field); // hide it again

    menu.menu("setIcon", {
      target: menuItem.target,
      iconCls: "icon-mini-add"
    });
  }

    $(dg).datagrid("resize");
}

function getMenuTitleFromColumn(col) {
  var title = col.title;
  if (!title || title === undefined || title.length == 0) {
    title = col.field.replace(/_/g, " ");
    title = title.charAt(0).toUpperCase() + title.substring(1);
  }
  return title;
}

$.extend($.fn.datagrid.methods, {
  resetColumnVisibility: function(dg) {
    var opts = $(dg).datagrid("options");
    var colIndex = opts.columns.length - 1;
    var cols = opts.columns[colIndex];
    var menu = $(opts.toolbar + " > .easyui-menubutton").menubutton("options")
      .menu;
    cols.map(function(col) {
      var title = getMenuTitleFromColumn(col);

      if (col.hiddenDefault === true) {
        $(dg).datagrid("hideColumn", col.field); // show the column
        var menuItem = $(menu).menu("findItem", title);
        $(menu).menu("setIcon", {
          target: menuItem.target,
          iconCls: "icon-mini-add"
        });
      } else {
        $(dg).datagrid("showColumn", col.field); // show the column
        var menuItem = $(menu).menu("findItem", title);
        $(menu).menu("setIcon", { target: menuItem.target, iconCls: "" });
      }
    });

    $(dg).datagrid("resize");
  },
  showAllColumns: function(dg) {
    var opts = $(dg).datagrid("options");
    var colIndex = opts.columns.length - 1;
    var cols = opts.columns[colIndex];
    var menu = $(opts.toolbar + " > .easyui-menubutton").menubutton("options")
      .menu;
    cols.map(function(col) {
      var title = getMenuTitleFromColumn(col);

      $(dg).datagrid("showColumn", col.field); // show the column
      var menuItem = $(menu).menu("findItem", title);
      $(menu).menu("setIcon", { target: menuItem.target, iconCls: "" });
    });

    $(dg).datagrid("resize");
  },

  hideAllColumns: function(dg) {
    var opts = $(dg).datagrid("options");
    var colIndex = opts.columns.length - 1;
    var cols = opts.columns[colIndex];
    var menu = $(opts.toolbar + " > .easyui-menubutton").menubutton("options")
      .menu;
    cols.map(function(col) {
      var title = getMenuTitleFromColumn(col);
      $(dg).datagrid("hideColumn", col.field); // show the column
      var menuItem = $(menu).menu("findItem", title);
      $(menu).menu("setIcon", {
        target: menuItem.target,
        iconCls: "icon-mini-add"
      });
    });
  },

  generateToggleColumn: function(dg) {
    setTimeout(function() {
      var opts = $(dg).datagrid("options");
      var colIndex = opts.columns.length - 1;
      var cols = opts.columns[colIndex];
      var menu = $(opts.toolbar + " > .easyui-menubutton").menubutton("options")
        .menu;

      $(menu).menu("appendItem", {
        text: "Reset to default",
        onclick: function(e) {
          $(dg).datagrid("resetColumnVisibility");
        }
      });
      $(menu).menu("appendItem", {
        text: "Show all",
        onclick: function(e) {
          $(dg).datagrid("showAllColumns");
        }
      });

      $(menu).menu("appendItem", {
        text: "Show none",
        onclick: function(e) {
          $(dg).datagrid("hideAllColumns");
        }
      });
      $(menu).menu("appendItem", { separator: true });

      cols.map(function(col) {
        var title = getMenuTitleFromColumn(col);
        col.hiddenDefault = col.hidden; // preserve state to be able to reset it
        $(menu).menu("appendItem", {
          text: title,
          iconCls:
            col.hidden !== undefined && col.hidden === true
              ? "icon-mini-add"
              : "",
          field: col.field,
          onclick: function(e) {
            var menuItem = $(menu).menu("findItem", title);
            toggleDgColumn(menuItem.target, dg.selector.replace("#", ""));
          }
        });
      });
    }, 0);
  }
});
