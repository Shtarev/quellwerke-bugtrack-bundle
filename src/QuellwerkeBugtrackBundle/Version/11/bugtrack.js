pimcore.registerNS("pimcore.plugin.myBundle");

/* Frontend bug list */
var originalError = console.error; 
var originalErrorAdd = null;

console.error = function (...args) {
    originalError.apply(console, args);
    originalErrorAdd = JSON.stringify({
        errorLog: args.map(a => String(a)).join(' ')
    });
};

function frontLog() {
        let result = null
        let itemType = null;
        let id = null;
        let tab = null;
        let subTab = null;

        let tabPanel = Ext.getCmp("pimcore_panel_tabs");
        let activeTab = tabPanel.getActiveTab();

        if(activeTab !== undefined) {

            /* if object */
            if(activeTab.object !== undefined) {
                itemType = 'Object';
                id = activeTab.object.id;
            }

            /* if asset */
            if(activeTab.asset !== undefined) {
                itemType = 'Asset';
                id = activeTab.asset.id;
            }

            /* if document */
            if(activeTab.document !== undefined) {
                itemType = 'Document';
                id = activeTab.document.id;
            }

            let innerTabPanel = activeTab.down("tabpanel");
            let activeInnerTab = innerTabPanel.getActiveTab();
            tab = activeInnerTab.title;

            let innerTabPanelSub = activeInnerTab.down("tabpanel");
            if(innerTabPanelSub !== null) {
                let activeInnerTabSub = innerTabPanelSub.getActiveTab();
                subTab = activeInnerTabSub.title;
            }
        }

        result = JSON.stringify({
            activeTab: {
                itemType: itemType,
                id: id,
                tab: tab,
                subTab: subTab
            },
            originalError: originalErrorAdd,
        });

        return result;
};

/* Button and submission */
pimcore.plugin.myBundle = Class.create({
    initialize: function () {
        document.addEventListener(
            pimcore.events.preMenuBuild,
            this.preMenuBuild.bind(this)
        );
    },

    preMenuBuild: function (e) {
        const menu = e.detail.menu;

        menu.my_bundle = {
            label: "My Bundle",
            iconCls: "pimcore_nav_icon_info", // icon
            priority: 150,
            shadow: false,
            noSubmenus: true,

            handler: function () {
                const win = new Ext.Window({
                    title: "Notify about the error",
                    width: 400,
                    modal: true,
                    layout: "fit",
                    closeAction: "destroy",
                    items: [
                        {
                            xtype: "form",
                            bodyPadding: 10,
                            defaults: {
                                anchor: "100%",
                                labelWidth: 120
                            },
                            items: [
                                {
                                    xtype: "textfield",
                                    fieldLabel: "Your message",
                                    name: "my_input",
                                    allowBlank: false,
                                    emptyText: "Enter something..."
                                }
                            ]
                        }
                    ],
                    buttons: [
                        {
                            text: "OK",
                            iconCls: "pimcore_icon_apply",
                            handler: function () {

                                const form = win.down("form").getForm();

                                if (!form.isValid()) {
                                    return;
                                }

                                const values = form.getValues();

                                //console.log("Input value:", values.my_input);

                                Ext.Ajax.request({
                                    url: "/admin/bugtrack/bugs",
                                    method: "POST",
                                    params: {
                                        value: values.my_input,
                                        frontLog: frontLog() ////////////////
                                    },
                                    success: function (response) {
                                        let data = Ext.decode(response.responseText);
                                        pimcore.helpers.showNotification(
                                            "Success",
                                            data.result,
                                            "success"
                                        );
                                        // create and download a file
                                        let json = JSON.stringify(data, null, 2);
                                        let blob = new Blob([json], { type: "application/json" });
                                        let url = URL.createObjectURL(blob);

                                        let a = document.createElement("a");
                                        a.href = url;
                                        a.download = data.fileName;
                                        a.click();

                                        URL.revokeObjectURL(url);
                                    }
                                });
                                
                                win.close();
                            }
                        },
                        {
                            text: "Cancel",
                            iconCls: "pimcore_icon_cancel",
                            handler: function () {
                                win.close();
                            }
                        }
                    ]
                });
                win.show();
            }
        };
    }
});

new pimcore.plugin.myBundle();
