$(document).ready(function() {
    // discard alert after 5s
    setTimeout(function(){
        $('.alert').fadeOut();
    },5000);

        // taxonomy navigation (tabs)
    $( "#taxonomy-editor-extension" ).on('click', '.filter', function(e) {
        e.preventDefault();

        var allf = $('.tabgrouping');
        var customType = $( this ).data('filter');
        allf
            .hide()
            .filter(function () {
                return $(this).data('tab') === customType;
            })
            .show();
        $( '#filtertabs li' ).removeClass( "active" );
        $( this ).parent().attr('class', 'active');
    });

    $('.sortable').sortable();
    // menu-tabs
    //$('.nav-tabs').on("click", "a", function (e) {
        //e.preventDefault();
        //$(this).tab('show');
    //});

    // nestable
    //$('.me-menu').nestable({
        //'maxDepth': 100,
        //'threshold': 15
    //});

    // select2
    //$("div.me-addct").select2({
        //placeholder: trans_searchitem,
        //minimumInputLength: 3,
        //ajax: {
            //type: 'post',
            //url: '',
            //dataType: 'json',
            //quietMillis: 100,
            //data: function(term, page) {
                //return {
                    //action: 'search-contenttypes',
                    //ct: $('select.me-addct-filter').val(),
                    //q: term,
                    //page_limit: 100
                //};
            //},
            //results: function(data, page) {
                //var records = Array();
                //for (record in data.records[0]) {
                    //records.push({id:data.records[0][record].values.id, text:data.records[0][record].values.title, slug:data.records[0][record].values.slug, contenttype:data.records[0][record].contenttype.slug});
                //}

                //$('.select2-container.me-addct').removeClass('select2error');

                //return {results: records}
            //}
        //}
    //});

    //$('select.me-addsp').select2({
        //placeholder: trans_additem
    //});

    // toggle item edit-panel
    $( document ).on( "click", ".dd-edit", function() {
        var el = $(this).parent().children('div.dd-editpanel');
        var show = false;
        if ($(el).hasClass('hidden')) {
            show = true;
        }

        $('div.dd-editpanel').addClass('hidden');

        if (show) {
            $(el).removeClass('hidden');
        } else {
            $(el).addClass('hidden');
        }
    });

    // prefill label for contenttypes
    //$(".me-addct").on('change', function() {
        //var slug = $($(this).select2("data"))[0].slug;
        //$("#me-addct-label").val($(this).select2("data").text);
        //$("#me-addct-path").val($($(this).select2("data"))[0].contenttype + "/" + slug)
    //});

    //$(".me-addct-filter").on('change', function() {
        //$(this).parent().find('.select2-choice').addClass('select2-default');
        //$(this).parent().find('.select2-chosen').html(trans_searchitem);
        //$('#me-addct-label').val("");
        //$('#me-addct-path').val("");
        //$('#me-addct-class').val("");
        //$('#me-addct-title').val("");
    //});

    // prefill label for specialpages
    //$(".me-addsp").on('change', function() {
        //$('#me-addsp-label').val($(this).select2("data").text);
        //$('#me-addsp-path').val($(this).select2("val"));
    //});

    // add new menu
    //$('button#me-addmenu').click(function() {
        //var menuname = $('input#me-addmenu-name').val();
        //menuname = menuname.replace(/[^a-z0-9-_]/gi, "");

        //if (menuname == '') {
            //return false;
        //}

        //var existingMenus = [];
        //$('.me-menu').each(function() {
            //existingMenus.push($(this).data('menuname'));
        //})

        //if ($.inArray( menuname, existingMenus ) > -1) {
            //bootbox.alert(trans_menualreadyexists, function() {});
            //return false;
        //} else {
            //$(".nav-tabs li:last").before('<li><a href="#me-tab-'+ existingMenus.length +'" data-toggle="tabs">' + menuname +'</a></li>');
            //$(".tab-content div:last").before('<div class="tab-pane" id="me-tab-' + existingMenus.length +'"><div class="dd me-menu" id="me-menu-'+ existingMenus.length +'" data-menuname="'+ menuname +'"><ol class="dd-list me-menulist"></ol></div></div>');

            //$('input#me-addmenu-name').val('')

            //// activate new menu
            //$(".nav-tabs li").removeClass('active');
            //$(".nav-tabs li:last").prev("li").addClass('active');
            //$('.tab-content div').removeClass('active');
            //$(".tab-content div:last").prev("div").addClass('active');

            //$('.me-menu').nestable({
                //'maxDepth': 100,
                //'threshold': 15
            //});
        //}
    //});


    // add an item to the menu
    $(".me-additem").click(function() {
        var template = '<li class="dd-item dd3-item" [attributes] style="display: none;"><div class="dd-handle dd3-handle"></div><div class="dd3-content">[label]</div><div class="dd-edit dd3-edit pull-right"></div><div class="dd-editpanel well hidden">[editTemplate]<button type="button" class="btn btn-primary me-updateitem">'+ trans_save +'</button> <button type="button" class="btn btn-danger me-deleteitem">'+ trans_removefromtaxonomy +'</button></div></li>';

        var editTemplate = '<div class="left-inner-addon"><i class="fa fa-tag"></i><input class="me-input" type="text" data-tag="label" value="[label]" placeholder="'+ trans_label +'"></div><div class="left-inner-addon"><i class="fa fa-font"></i><input class="me-input" type="text" data-tag="name" value="[name]" placeholder="addname"></div>';

        var item = template;

        // if label was empty, it was properly set above. Still, display as <em>no label set</em>
        if ($('#' + this.id + '-label').val() == '') {
            var label = '<em>' + trans_nolabelset +'</em>';
        } else {
            var label = $('#' + this.id + '-label').val();
        }
        item = item.replace('[label]', label);

        var attributes = '';
            attributes = 'data-label="'+ $('#me-add-label').val() + '" data-name="'+ $('#me-add-name').val() + '"';

            editTemplate = editTemplate.replace('[label]', $('#me-add-label').val());
            editTemplate = editTemplate.replace('[name]', $('#me-add-name').val());

            $('#me-add-label').val("");
            $('#me-add-name').val("");

        item = item.replace('[attributes]', attributes);
        item = item.replace('[editTemplate]', editTemplate);

        // append to menu
        var activeTab = $("#filtertabs li.active a").data('filter').replace("me-tab-", "");

        $('#me-menu-' + activeTab +' ol:first-child').append(item);
        $('#me-menu-' + activeTab +' ol:first-child li:last').fadeIn(700);
    });

    // remove item from menu
    $(".tab-content").on("click", "button.me-deleteitem", function(e) {
        e.preventDefault();
        var item = $(this).parent().parent();

        if ($(item).find("ol:first").length >= 1) {

            bootbox.confirm(trans_deleteWithSubmenus, function(result) {
                if (true === result) {
                    removeMenuItem(item)
                }
            });
        } else {
            removeMenuItem(item);
        }
    })

    // actually removes an item from the menu
    function removeMenuItem(item) {
        var parentOl = $(item).parent('ol');
        parentOl = parentOl[0];
        $(item).remove();

        if ($(parentOl).find("li").length == 0 && !$(parentOl).hasClass('me-menulist')) {
            // last element
            $(parentOl).parent().find("> button").each(function() {
                this.remove();
            });

            parentOl.remove();
        }
    }

    // update item data
    $(".tab-content").on("click", "button.me-updateitem", function() {
        var item = $(this).parent('div').parent();

        $(this).parent('div').find('input').each(function() {
            var tag = $(this).data('tag');
            if (tag != undefined) {
                // replace parent data
                if (tag == 'name') {
                $(item).attr('data-' + tag, $(this).val().trim().replace(/\s+/g, '_'));
                }else{
                $(item).attr('data-' + tag, $(this).val());
                }

                if (tag == 'label') {
                    // update label
                    var label = $(item).children(".dd3-content").first();
                    if ($(this).val() == '') {
                        $(label).html('<em>' + trans_nolabelset +'</em>');
                    } else {
                        $(label).html($(this).val());
                    }
                }
            }
        });

        // close all editpanels
        $('div.dd-editpanel').addClass('hidden');
    });

    // confirm revert
    $('.revert-changes').click(function(e) {
        e.preventDefault();

        bootbox.confirm(trans_revertChanges, function(result) {
            if (true === result) {
                location.reload(true);
            }
        });

    })

    // restore menu.yml
    $(".restoremenus").on("click", function(e) {
        e.preventDefault();

        var filetime = $(this).data('filetime');

        bootbox.confirm(trans_restorebackup, function(result)
        {
            if (true === result) {
                restoreBackup(filetime);
            }
        });
    });

    // saves the menu(s)
    $("#savemenus").click(function() {
        //if (me_writeLock == 0) {
            //return false;
        //}

        var menus = {};

        $('.me-menu').each(function() {
            var menu = [];
            $(this).children('ol').children('li').each(function(){
                menu.push(extractMenuItem(this));
            });

            var menuName = $(this).data('menuname');
            menus[menuName] = menu;
        });

        $.ajax({
            url: '',
            type: 'POST',
            data: {'taxonomies': menus, 'writeLock': me_writeLock},
            dataType:"json",
            error: function(data) {
                bootbox.alert(trans_connectionError, function() {});
            },
            success: function (data) {
                if(typeof data.writeLock != 'undefined') {
                    me_writeLock = data.writeLock;
                }

                // all went well, refresh page
                if (data.status == 0) {
                    location.reload(true);
                }

                // menu.yml was edited in the meantime
                if (data.status == 1) {
                    bootbox.alert(trans_writeLockError, function() {});
                }

                // xhr-error or parse-error
                if (data.status == 2 || data.status == 4) {
                    bootbox.alert(trans_parseError, function() {});
                }

                // unable to write menu.yml
                if (data.status == 3) {
                    bootbox.alert(trans_writeError, function() {});
                }

                // backup failed
                if (data.status == 5) {
                    bootbox.alert(trans_backupFailError, function() {});
                }
            }
        });
    });

});

/**
 * Extract an item with all its possible subitems
 *
 * @param item
 * @returns {{}}
 */
function extractMenuItem(item)
{
    var extractedItem = {};

    // extract data attributes
    $.each(item.attributes, function() {
        if (match = this.name.match(/^data-([a-z]+)/)) {
            if (this.value != '' || match[1] == 'label'|| match[1] == 'name' || match[1] == 'originalname') {
                extractedItem[match[1]] = this.value;
            }
        }
    });

     //loop subs
    //var subs = $("> ol > li", item);
    //if (subs.length > 0) {
        //var itemsSub = [];

        //subs.each(function() {
            //itemsSub.push(extractMenuItem(this));
        //})
        //extractedItem['submenu'] = itemsSub;
    //}

    return extractedItem;
}

/**
 * Restores a previously saved backup
 *
 * @param filetime
 */
function restoreBackup(filetime)
{
    $.ajax({
        url: '',
        type: 'POST',
        data: {'filetime': filetime},
        dataType:"json",
        error: function(data) {
            bootbox.alert(trans_connectionError, function() {});
        },
        success: function (data) {
            // all went well, refresh page
            if (data.status == 0) {
                location.reload(true);
            } else {
                bootbox.alert(trans_backupRestoreFailError + ' ' + data.error, function() {});
            }
        }
    });

}
