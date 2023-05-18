function graphicConstruct(tempArray) {
    var labels = [];
    var cpu = [];
    var gpu = [];
    var sensation = [];
    var temperature = [];
    
    Array.from(tempArray.result).reverse().map(t => {
        let date = new Date(t.dateTime);
       
        labels.push(date.toLocaleDateString() + " " + date.toLocaleTimeString());
        cpu.push(parseFloat(t.cpu));
        gpu.push(parseFloat(t.gpu));
        sensation.push(parseFloat(t.sensation));
        temperature.push(parseFloat(t.temperature));
    });

    var data = {
        labels: labels,
        datasets: [
            {
                label: 'CPU',
                backgroundColor: 'rgba(0,100,255,0.5)',
                borderColor: 'rgb(0,100,255)',
                data: cpu
            },
            {
                label: 'GPU',
                backgroundColor: 'rgba(255,99,132,0.5)',
                borderColor: 'rgb(255,99,132)',
                data: gpu
            },
            {
                label: 'Sensation',
                backgroundColor: 'rgba(0,100,255,0.5)',
                borderColor: 'rgb(0,100,255)',
                data: sensation
            },
            {
                label: 'Temperature',
                backgroundColor: 'rgba(255,99,132,0.5)',
                borderColor: 'rgb(255,99,132)',
                fill: true,
                data: temperature
            }
        ]
    };

    const config = {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            onClick: function (event, element) {
                const TAG_SPINNER = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Carregando...</span></div>';

                modalDateTime = $('#modalDateTime').html(TAG_SPINNER);                          //01
                modalPingJitter = $('#modalPingJitter').html(TAG_SPINNER);                      //02
                modalPingLatency = $('#modalPingLatency').html(TAG_SPINNER);                    //03
                modalDownloadBandwidth = $('#modalDownloadBandwidth').html(TAG_SPINNER);        //04
                modalDownloadBytes = $('#modalDownloadBytes').html(TAG_SPINNER);                //05
                modalDownloadElapsed = $('#modalDownloadElapsed').html(TAG_SPINNER);            //06
                modalUploadBandwidth = $('#modalUploadBandwidth').html(TAG_SPINNER);            //07
                modalUploadBytes = $('#modalUploadBytes').html(TAG_SPINNER);                    //08
                modalUploadElapsed = $('#modalUploadElapsed').html(TAG_SPINNER);                //09
                modalPacketLoss = $('#modalPacketLoss').html(TAG_SPINNER);                      //10
                modalIsp = $('#modalIsp').html(TAG_SPINNER);                                    //11
                modalInterfaceInternalIp = $('#modalInterfaceInternalIp').html(TAG_SPINNER);    //12
                modalInterfaceName = $('#modalInterfaceName').html(TAG_SPINNER);                //13
                modalInterfaceMacAddr = $('#modalInterfaceMacAddr').html(TAG_SPINNER);          //14
                modalInterfaceVpn = $('#modalInterfaceVpn').html(TAG_SPINNER);                  //15
                modalInterfaceExternalIp = $('#modalInterfaceExternalIp').html(TAG_SPINNER);    //16
                modalServerIp = $('#modalServerIp').html(TAG_SPINNER);                          //17
                modalServerName = $('#modalServerName').html(TAG_SPINNER);                      //18
                modalServerLocation = $('#modalServerLocation').html(TAG_SPINNER);              //19
                modalServerCountry = $('#modalServerCountry').html(TAG_SPINNER);                //20
                modalServerSelected = $('#modalServerSelected').html(TAG_SPINNER);              //21
                modalResultUrl = $('#modalResultUrl').html(TAG_SPINNER);                        //22

                $('#modalDetalhe').modal('show');

                $.post(URL_JSON_DETAIL, {dateTime: graphic.data.labels[element[0].index]})
                        .done(function (data) {                            
                            if (data.message === 'success') {
                                let d = new Date(data.speedtest.datetime);

                                modalDateTime.text(d.toLocaleDateString() + " " + d.toLocaleTimeString());                                                                                                                          //01
                                modalPingJitter.text(parseFloat(data.speedtest.pingJitter).toLocaleString(LOCALE) + " ms");                                 //02
                                modalPingLatency.text(parseFloat(data.speedtest.pingLatency).toLocaleString(LOCALE) + " ms");                               //03
                                modalDownloadBandwidth.text(parseFloat(data.speedtest.downloadBandwidth * 8 / 1048576).toLocaleString(LOCALE) + " Mbps");   //04
                                modalDownloadBytes.text(parseFloat(data.speedtest.downloadBytes).toLocaleString(LOCALE) + " bytes");                        //05
                                modalDownloadElapsed.text(parseFloat(data.speedtest.downloadElapsed).toLocaleString(LOCALE));                               //06
                                modalUploadBandwidth.text(parseFloat(data.speedtest.uploadBandwidth * 8 / 1048576).toLocaleString(LOCALE) + " Mbps");       //07
                                modalUploadBytes.text(parseFloat(data.speedtest.uploadBytes).toLocaleString(LOCALE) + " bytes");                            //08
                                modalUploadElapsed.text(parseFloat(data.speedtest.uploadElapsed).toLocaleString(LOCALE));                                   //09
                                modalPacketLoss.text(parseFloat(data.speedtest.packetLoss).toLocaleString(LOCALE) + "%");                                   //10
                                modalIsp.text(data.speedtest.isp);                                                                                          //11
                                modalInterfaceInternalIp.text(data.speedtest.interfaceInternalIp);                                                          //12
                                modalInterfaceName.text(data.speedtest.interfaceName);                                                                      //13
                                modalInterfaceMacAddr.text(data.speedtest.interfaceMacAddr);                                                                //14
                                modalInterfaceVpn.text((data.speedtest.interfaceVpn) ? data.speedtest.interfaceVpn : LABEL_NO);                             //15
                                modalInterfaceExternalIp.text(data.speedtest.interfaceExternalIp);                                                          //16
                                modalServerIp.text(data.speedtest.serverIp);                                                                                //17
                                modalServerName.text(data.speedtest.speedtestServer.name);                                                                  //18
                                modalServerLocation.text(data.speedtest.speedtestServer.location);                                                          //19
                                modalServerCountry.text(data.speedtest.speedtestServer.country);                                                            //20
                                modalServerSelected.text((data.speedtest.speedtestServer.selected) ? LABEL_YES : LABEL_NO);                                 //21
                                modalResultUrl.html("<a href='" + data.speedtest.resultUrl + "' target='_blank'><i class='bi bi-activity'></i> Speedtest report</a>"); //22
                            }
                        }).fail(function(data,status,j){
                            console.log(data,status,j);
                        });
            },            
            responsive: true,
            elements: {
                line: {
                    borderWidth: 1
                },
                point: {
                    pointStyle: 'circle',
                    pointRadius: 1,
                    pointHoverRadius: 5
                }
            },
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    position: 'average',
                    callbacks: {
                        label: function (tooltipItems) {
                            return " " +
                                    tooltipItems.dataset.label +
                                    ": " +
                                    tooltipItems.raw + " ºC";
                        }
                    }
                }
            }
        }
    };
    const graphic = new Chart(document.getElementById('temperature'), config);
}

function graphicShow(days) {
    $.post(URL_JSON_DAYS, {days: days})
            .done(function (data, textStatus, jqXHR) {
                graphicConstruct(data);
            })
            .fail(function (data) {
                console.log("Message", data.responseText);
            });
}