// @todo remove some unnecissary self references.
// @todo enter key will submit forms without going trough the code. Fix that.

addFitDetailhover = function ()
{
    $('li.fit').hover(function(){
        $('div.detail',this).show();
    },function(){
        $('div.detail',this).hide();
    })
}

// helper object to contain functions for Setup functionality
var setupHelper = function(setupDiv)
{
    this.id = $(setupDiv).attr('id');
    this.selfEl = setupDiv;
    this.name = $('.setupName',setupDiv).text();
    this.self = this;
    this.fitListEl = null;
    this.dialogEl = $("<div></div>");
    this.deleteDialog = $('<div id="dialog-confirm" title="Delete Setup?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This will permanently remove "'+this.name+'". Are you sure?</p></div>');

    this.refreshUrl = $('.refreshSetup', $('#'+this.id)).attr('href');

    this.init = function ()
    {
        var self = this;
        var selfEl = $('#'+this.id);
        self.selfEl = selfEl;
        $('.refreshSetup', selfEl).off('click');
        $('.refreshSetup', selfEl).click(function() { self.refresh(self); return false; });


        $('.deleteSetup', selfEl).off('click');
        $('.deleteSetup', selfEl).click(function(){
            var url = this.href;

            self.deleteDialog.dialog({
                resizable: false,
                modal: true,
                buttons: {
                    "Delete Setup": function() {
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

        $('.addFit', selfEl).off('click');
        $('.addFit', selfEl).click(function(){
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


    }

    this.dialogClose = function(self)
    {
        self.dialogEl.dialog("close");
    }

    this.addNewFit = function (self)
    {
        $('#addFit_form').ajaxForm(function() {
            self.refresh(self);
            self.dialogClose(self);

            $('#addFit_form').remove();
        }).submit();
    }

    this.refresh = function (self)
    {
        $.get(
            self.refreshUrl,
            function(data)
            {
                $(self.selfEl).replaceWith(data);
                self.init();
                addFitDetailhover()
            }
        );
    }
}


// helper object to contain functionality for the main dashboard
var dashboardHelper = function(){

    this.self = this;
    this.dialogEl = $("<div></div>"),

    // Will contain all the setup helpers
    this.setups = [];

    // initialize all the buttons and stuffs.
    this.init = function ()
    {
        var self = this;

        $('#refreshSetups').click(function(){self.refreshSetups(self)});

        $('#quickAddSetup').click(function(){
            var url = this.href;
            $.get(
                url,
                function (htmlForm) {
                    self.dialogEl
                        .html(htmlForm)
                        .dialog({
                            autoOpen: true,
                            title: 'Add new Setup',
                            height: 750,
                            width:  500,
                            buttons: [
                                { text: "Add new setup", click: function () { self.addNewSetup(self) } },
                                { text: "Close", click: function() { self.dialogClose(self) } }
                            ]
                        });
                    return false;
                });
            return false;
        });

        // initiate setuphelpers for each setup
        $('#setupList .setup').each(function(){
            var setup = new setupHelper(this);
            setup.init();
            self.addSetup(setup);
        })
    }

    this.dialogClose = function(self)
    {
        self.dialogEl.dialog("close");
    }

    this.addNewSetup = function (self)
    {
        $('#setupAdd_form').ajaxForm(function() {
            self.refreshSetups(self);
            self.dialogClose(self);

        }).submit();
    }

    this.refreshSetups = function (self)
    {
        $.get(
            'index.php/setup/list',
            function(data)
            {
                $("#setupList").html(data);

                $('#setupList .setup').each(function(){
                    var id = $(this).attr('id');
                    if (id in self.setups)
                    {
                        self.setups[id].refresh(self.setups[id]);
                    }
                    else
                    {
                        var setup = new setupHelper(this);
                        setup.init();
                        self.addSetup(setup);
                    }

                });
                addFitDetailhover()

            }
        );
    }

    this.addSetup = function (setup)
    {
        this.setups[setup.id] = setup;
    }
}


