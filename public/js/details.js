
// helper object to contain functionality for the main dashboard
var setupDetailHelper = function(){

    this.self = this;
    this.listEl = null,
        this.dialogEl = $("<div></div>"),

        this.setupId =0;

    // Will contain all the setup helpers
    this.setups = [];

    // Will contain some urls.
    this.url = {};

    // initialize all the buttons and stuffs.
    this.init = function (mainEl, fitListEl)
    {
        var self = this;
        this.listEl = fitListEl;


        $('.mainheader .addFit', mainEl).click(function(){
            var url = this.href;
            $.get(
                url,
                function (htmlForm) {
                    self.dialogEl
                        .html(htmlForm)
                        .dialog({
                            autoOpen: true,
                            title: 'Add new fit',
                            buttons: [
                                { text: "Add new fit", click: function () { self.addNewFit(self) } },
                                { text: "Close", click: function() { self.dialogClose(self) } }
                            ]
                        });
                    return false;
                });
            return false;
        });

        self.url.refresh = $('.mainheader .refreshSetup').attr('href');
        $('.mainheader .refreshSetup').click(function(){self.refreshFits(self); return false;});
//
        // initiate setuphelpers for each setup
        $('#fitList .box').each(function(){
            var helper = new boxHelper(this);
            helper.init();
            self.addSetup(helper);
        })
    }

    this.dialogClose = function(self)
    {
        self.dialogEl.dialog("close");
    }

    this.addNewFit = function (self)
    {
        $('#addFit_form').ajaxForm(function() {
            self.refreshFits(self);
            self.dialogClose(self);
        }).submit();
    }

    this.refreshFits = function (self)
    {
        var pos = $("#setupDetails").offset();
        $('<div class="overlay" id="overlay"></div>')
            .css({top: pos.top, left: pos.left, width: $("#setupDetails").width(), height: $("#setupDetails").height()})
            .appendTo('body');

        $.get(
            self.url.refresh,
            function(data)
            {
                $("#setupDetails").html(data);

                $('#fitList .fit').each(function(){
                    var id = $(this).attr('id');
                    if (id in self.setups)
                    {
                        self.setups[id].init();
                    }
                    else
                    {
                        var setup = new boxHelper(this);
                        setup.init();
                        self.addSetup(setup);
                    }

                });
                addFitDetailhover()

                $('#overlay').remove();
            }
        );
    }

    this.addSetup = function (setup)
    {
        this.setups[setup.id] = setup;
    }
}

// helper object to contain functions for Setup functionality
var boxHelper = function(Div)
{
    this.id = $(Div).attr('id');
    this.selfEl = Div;
    this.name = $('.name',Div).text();
    this.self = this;
    this.fitListEl = null;
    this.dialogEl = $("<div></div>");
    this.deleteDialog = $('<div id="dialog-confirm" title="Delete?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This will permanently remove "'+this.name+'". Are you sure?</p></div>');

    this.refreshUrl = $('.refreshFit', $('#'+this.id)).attr('href');

    this.urls = {};

    this.init = function ()
    {
        var self = this;
        var selfEl = $('#'+this.id);
        self.selfEl = selfEl;

        self.urls.refresh = $('.refresh', selfEl).attr('href');
        $('.refresh', selfEl).off('click');
        $('.refresh', selfEl).click(function() {
            var url = this.href;
            self.refresh(self);
            return false;
        });

        $('.delete', selfEl).off('click');
        $('.delete', selfEl).click(function(){
            var url = this.href;

            self.deleteDialog.dialog({
                resizable: false,
                modal: true,
                buttons: {
                    "Delete": function() {
                        $.get(url);
                        $('#'+self.id).remove();
                        $( this ).dialog( "close" );
                    },
                    Cancel: function() {
                        $( this ).dialog( "close" );
                    }
                }
            });

            return false;
        });

        $('.add', selfEl).off('click');
        $('.add', selfEl).click(function(){
            var url = this.href;
            $.get(
                url,
                function (htmlForm) {
                    self.dialogEl
                        .html(htmlForm)
                        .dialog({
                            autoOpen: true,
                            title: 'Add new fit',
                            buttons: [
                                { text: "Add new fit", click: function () { self.submitFitForm('#addFit_form', self) } },
                                { text: "Close", click: function() { self.dialogClose(self) } }
                            ]
                        });
                    return false;
                });
            return false;
        });

        $('.edit', selfEl).off('click');
        $('.edit', selfEl).click(function(){
            var url = this.href;
            $.get(
                url,
                function (htmlForm) {
                    self.dialogEl
                        .html(htmlForm)
                        .dialog({
                            autoOpen: true,
                            title: 'Edit fit',
                            buttons: [
                                { text: "Update fit", click: function () { self.submitFitForm('#editFit_form', self) } },
                                { text: "Close", click: function() { self.dialogClose(self) } }
                            ]
                        });
                    return false;
                });
            return false;
        });
    }

    this.dialogClose = function(self)
    {
        self.dialogEl.dialog("close");
    }

    this.submitFitForm = function (formId, self)
    {
        $(formId).ajaxForm(function() {
            self.refresh(self);
            self.dialogClose(self);

            $(formId).remove();
        }).submit();
    }

    this.refresh = function (self)
    {
        var pos = $(self.selfEl).offset();
        var overlayId = "overlay"+self.id;

        $('<div class="overlay" id="'+overlayId+'"></div>')
            .css({top: pos.top, left: pos.left, width: $(self.selfEl).width(), height: $(self.selfEl).height()})
            .appendTo('body');

        $.get(
            self.urls.refresh,
            function(data)
            {
                $(self.selfEl).replaceWith(data);
                self.init();
                addFitDetailhover()

                $('#'+overlayId).remove();
            }
        );
    }
}