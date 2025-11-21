function newdonutChart(id, value, labels, legendPosition = "bottom") {
    var isDarkMode = document.documentElement.classList.contains('theme-dark');
    var textColor = isDarkMode ? '#a7abc3' : '#373d3f';

    var options = {
        series: value,
        labels: labels,
        colors: ["#93f0cf", "#99d5ff", "#a97dc4", "#167bc3"],
        chart: {
            width: "100%",
            height: 420,
            type: "donut",
        },
        theme: {
            mode: isDarkMode ? 'dark' : 'light'
        },
        responsive: [
            {
                breakpoint: undefined,
                options: {},
            },
        ],
        legend: {
            position: legendPosition,
            labels: {
                colors: textColor
            }
        },
        tooltip: {
            enabled: false,
        },
        plotOptions: {
            pie: {
                startAngle: 0,
                endAngle: 360,
                expandOnClick: true,
                offsetX: 0,
                offsetY: 0,
                customScale: 1,
                dataLabels: {
                    offset: 0,
                    minAngleToShowLabel: 10,
                },
                donut: {
                    size: "65%",
                    background: "transparent",
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            fontSize: "15px",
                            fontFamily: "Helvetica, Arial, sans-serif",
                            fontWeight: 500,
                            color: textColor,
                            offsetY: -10,
                            formatter: function (val) {
                                return val;
                            },
                        },
                        value: {
                            show: true,
                            fontSize: "16px",
                            fontFamily: "Helvetica, Arial, sans-serif",
                            fontWeight: 700,
                            color: textColor,
                            offsetY: 16,
                            formatter: function (val) {
                                return val;
                            },
                        },
                        total: {
                            show: true,
                            showAlways: false,
                            label: "Total",
                            fontSize: "15px",
                            fontFamily: "Helvetica, Arial, sans-serif",
                            fontWeight: 500,
                            color: textColor,
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => {
                                    return a + b;
                                }, 0);
                            },
                        },
                    },
                },
            },
        },
    };
    var chart = new ApexCharts(document.querySelector(id), options);
    chart.render();
}
