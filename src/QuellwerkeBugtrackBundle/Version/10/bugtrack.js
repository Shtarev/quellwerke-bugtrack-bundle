pimcore.registerNS("quellwerke.bugtrack");

quellwerke.bugtrack = Class.create({
    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },
    pimcoreReady: function () {
        const injectButton = () => {
            const nav = document.querySelector("#pimcore_navigation ul");
            if (!nav) {
                return false;
            }
            if (document.getElementById("quellwerke_bugtrack_button")) {
                return true;
            }
            const li = document.createElement("li");
            li.id = "quellwerke_bugtrack_button";
            li.className = "pimcore_menu_item true-initialized";
            li.setAttribute("data-menu-tooltip", "Bugtrack");
            li.classList.add("pimcore_nav_icon_info"); // icon

            li.onclick = () => {
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
            };

            nav.appendChild(li);

            return true;
        };

        const observer = new MutationObserver(() => {
            injectButton();
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        injectButton();
    }
});

new quellwerke.bugtrack();