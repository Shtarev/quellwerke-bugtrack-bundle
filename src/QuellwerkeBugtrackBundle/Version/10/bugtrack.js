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
                                        frontLog: null // TODO: frontLog()
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
