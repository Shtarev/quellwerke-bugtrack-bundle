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
            iconCls: "pimcore_nav_icon_info", // icon: "/bundles/.../question.svg"
            priority: 150,
            shadow: false,
            noSubmenus: true,

            handler: function () {
                const win = new Ext.Window({
                    title: "My Dialog",
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
                                    fieldLabel: "Enter a value",
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

                                console.log("Input value:", values.my_input);

                                // You can call the Symfony endpoint here via AJAX.
                                Ext.Ajax.request({
                                    url: "/admin/my-endpoint", // TODO: This is the real address here.
                                    method: "POST",
                                    params: {
                                        value: values.my_input
                                    },
                                    success: function (response) {
                                        pimcore.helpers.showNotification(
                                            "Success",
                                            "The data has been sent",
                                            "success"
                                        );
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
