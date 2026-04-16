pimcore.registerNS("pimcore.plugin.myBundle");

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
                                        value: values.my_input
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
