'use strict';

(function() {

  // JS global variables from app.js file 
  const colors = window.config.colors;
  const fontFamily = window.config.fontFamily;

  const revenueChartData = [
    49.33,
    48.79,
    50.61,
    53.31,
    54.78,
    53.84,
    54.68,
    56.74,
    56.99,
    56.14,
    56.56,
    60.35,
    58.74,
    61.44,
    61.11,
    58.57,
    54.72,
    52.07,
    51.09,
    47.48,
    48.57,
    48.99,
    53.58,
    50.28,
    46.24,
    48.61,
    51.75,
    51.34,
    50.21,
    54.65,
    52.44,
    53.06,
    57.07,
    52.97,
    48.72,
    52.69,
    53.59,
    58.52,
    55.10,
    58.05,
    61.35,
    57.74,
    60.27,
    61.00,
    57.78,
    56.80,
    58.90,
    62.45,
    58.75,
    58.40,
    56.74,
    52.76,
    52.30,
    50.56,
    55.40,
    50.49,
    52.49,
    48.79,
    47.46,
    43.31,
    38.96,
    34.73,
    31.03,
    32.63,
    36.89,
    35.89,
    32.74,
    33.20,
    30.82,
    28.64,
    28.44,
    27.73,
    27.75,
    25.96,
    24.38,
    21.95,
    22.08,
    23.54,
    27.30,
    30.27,
    27.25,
    29.92,
    25.14,
    23.09,
    23.79,
    23.46,
    27.99,
    23.21,
    23.91,
    19.21,
    15.13,
    15.08,
    11.00,
    9.20,
    7.47,
    11.64,
    15.76,
    13.99,
    12.59,
    13.53,
    15.01,
    13.95,
    13.23,
    18.10,
    20.63,
    21.06,
    25.37,
    25.32,
    20.94,
    18.75,
    15.38,
    14.56,
    17.94,
    15.96,
    16.35,
    14.16,
    12.10,
    14.84,
    17.24,
    17.79,
    14.03,
    18.65,
    18.46,
    22.68,
    25.08,
    28.18,
    28.03,
    24.11,
    24.28,
    28.23,
    26.24,
    29.33,
    26.07,
    23.92,
    28.82,
    25.14,
    21.79,
    23.05,
    20.71,
    29.72,
    30.21,
    32.56,
    31.46,
    33.69,
    30.05,
    34.20,
    36.93,
    35.50,
    34.78,
    36.97
  ];

  const revenueChartCategories = [
    "Jan 01 2024", "Jan 02 2024", "jan 03 2024", "Jan 04 2024", "Jan 05 2024", "Jan 06 2024", "Jan 07 2024", "Jan 08 2024", "Jan 09 2024", "Jan 10 2024", "Jan 11 2024", "Jan 12 2024", "Jan 13 2024", "Jan 14 2024", "Jan 15 2024", "Jan 16 2024", "Jan 17 2024", "Jan 18 2024", "Jan 19 2024", "Jan 20 2024","Jan 21 2024", "Jan 22 2024", "Jan 23 2024", "Jan 24 2024", "Jan 25 2024", "Jan 26 2024", "Jan 27 2024", "Jan 28 2024", "Jan 29 2024", "Jan 30 2024", "Jan 31 2024",
    "Feb 01 2024", "Feb 02 2024", "Feb 03 2024", "Feb 04 2024", "Feb 05 2024", "Feb 06 2024", "Feb 07 2024", "Feb 08 2024", "Feb 09 2024", "Feb 10 2024", "Feb 11 2024", "Feb 12 2024", "Feb 13 2024", "Feb 14 2024", "Feb 15 2024", "Feb 16 2024", "Feb 17 2024", "Feb 18 2024", "Feb 19 2024", "Feb 20 2024","Feb 21 2024", "Feb 22 2024", "Feb 23 2024", "Feb 24 2024", "Feb 25 2024", "Feb 26 2024", "Feb 27 2024", "Feb 28 2024",
    "Mar 01 2024", "Mar 02 2024", "Mar 03 2024", "Mar 04 2024", "Mar 05 2024", "Mar 06 2024", "Mar 07 2024", "Mar 08 2024", "Mar 09 2024", "Mar 10 2024", "Mar 11 2024", "Mar 12 2024", "Mar 13 2024", "Mar 14 2024", "Mar 15 2024", "Mar 16 2024", "Mar 17 2024", "Mar 18 2024", "Mar 19 2024", "Mar 20 2024","Mar 21 2024", "Mar 22 2024", "Mar 23 2024", "Mar 24 2024", "Mar 25 2024", "Mar 26 2024", "Mar 27 2024", "Mar 28 2024", "Mar 29 2024", "Mar 30 2024", "Mar 31 2024",
    "Apr 01 2024", "Apr 02 2024", "Apr 03 2024", "Apr 04 2024", "Apr 05 2024", "Apr 06 2024", "Apr 07 2024", "Apr 08 2024", "Apr 09 2024", "Apr 10 2024", "Apr 11 2024", "Apr 12 2024", "Apr 13 2024", "Apr 14 2024", "Apr 15 2024", "Apr 16 2024", "Apr 17 2024", "Apr 18 2024", "Apr 19 2024", "Apr 20 2024","Apr 21 2024", "Apr 22 2024", "Apr 23 2024", "Apr 24 2024", "Apr 25 2024", "Apr 26 2024", "Apr 27 2024", "Apr 28 2024", "Apr 29 2024", "Apr 30 2024",
    "May 01 2024", "May 02 2024", "May 03 2024", "May 04 2024", "May 05 2024", "May 06 2024", "May 07 2024", "May 08 2024", "May 09 2024", "May 10 2024", "May 11 2024", "May 12 2024", "May 13 2024", "May 14 2024", "May 15 2024", "May 16 2024", "May 17 2024", "May 18 2024", "May 19 2024", "May 20 2024","May 21 2024", "May 22 2024", "May 23 2024", "May 24 2024", "May 25 2024", "May 26 2024", "May 27 2024", "May 28 2024", "May 29 2024", "May 30 2024",
  ]





  // Date Picker
  if (document.querySelector('#dashboardDate')) {
    flatpickr("#dashboardDate", {
      wrap: true,
      dateFormat: "d-M-Y",
      defaultDate: "today",
    });
  }
  // Date Picker - END





  // New Customers Chart
  const customersChartElement = document.querySelector('#customersChart');
  if (customersChartElement) {
    const customersChartOptions = {
      chart: {
        type: "line",
        height: 60,
        sparkline: {
          enabled: !0
        }
      },
      series: [{
        name: '',
        data: [3844, 3855, 3841, 3867, 3822, 3843, 3821, 3841, 3856, 3827, 3843]
      }],
      xaxis: {
        type: 'datetime',
        categories: ["Jan 01 2024", "Jan 02 2024", "Jan 03 2024", "Jan 04 2024", "Jan 05 2024", "Jan 06 2024", "Jan 07 2024", "Jan 08 2024", "Jan 09 2024", "Jan 10 2024", "Jan 11 2024",],
      },
      stroke: {
        width: 2,
        curve: "smooth"
      },
      markers: {
        size: 0
      },
      colors: [colors.primary],
    };
    const customersChart = new ApexCharts(customersChartElement, customersChartOptions);
    customersChart.render();
  }
  // New Customers Chart - END




  // Orders Chart
  const ordersChartElement = document.querySelector('#ordersChart');
  if (ordersChartElement) {
    const ordersChartOptions = {
      chart: {
        type: "bar",
        height: 60,
        sparkline: {
          enabled: !0
        }
      },
      plotOptions: {
        bar: {
          borderRadius: 2,
          columnWidth: "60%"
        }
      },
      colors: [colors.primary],
      series: [{
        name: '',
        data: [36, 77, 52, 90, 74, 35, 55, 23, 47, 10, 63]
      }],
      xaxis: {
        type: 'datetime',
        categories: ["Jan 01 2024", "Jan 02 2024", "Jan 03 2024", "Jan 04 2024", "Jan 05 2024", "Jan 06 2024", "Jan 07 2024", "Jan 08 2024", "Jan 09 2024", "Jan 10 2024", "Jan 11 2024",],
      },
    };
    const ordersChart = new ApexCharts(ordersChartElement, ordersChartOptions);
    ordersChart.render();
  }
  // Orders Chart - END




  // Growth Chart
  const growthChartElement = document.querySelector('#growthChart');
  if (growthChartElement) {
    const growthChartOptions = {
      chart: {
        type: "line",
        height: 60,
        sparkline: {
          enabled: !0
        }
      },
      series: [{
        name: '',
        data: [41, 45, 44, 46, 52, 54, 43, 74, 82, 82, 89]
      }],
      xaxis: {
        type: 'datetime',
        categories: ["Jan 01 2024", "Jan 02 2024", "Jan 03 2024", "Jan 04 2024", "Jan 05 2024", "Jan 06 2024", "Jan 07 2024", "Jan 08 2024", "Jan 09 2024", "Jan 10 2024", "Jan 11 2024",],
      },
      stroke: {
        width: 2,
        curve: "smooth"
      },
      markers: {
        size: 0
      },
      colors: [colors.primary],
    };
    const growthChart = new ApexCharts(growthChartElement, growthChartOptions);
    growthChart.render();
  }
  // Growth Chart - END




    // Revenue Chart
    const revenueChartElement = document.querySelector('#revenueChart');
    if (revenueChartElement) {
      const revenueChartOptions = {
        chart: {
          type: "line",
          height: '400',
          parentHeightOffset: 0,
          foreColor: colors.secondary,
          toolbar: {
            show: false
          },
        },
        colors: [colors.primary, colors.danger, colors.warning],
        grid: {
          padding: {
            bottom: -4,
          },
          borderColor: colors.gridBorder,
          xaxis: {
            lines: {
              show: true
            }
          }
        },
        series: [
          {
            name: "Revenue",
            data: revenueChartData
          },
        ],
        xaxis: {
          type: "datetime",
          categories: revenueChartCategories,
          lines: {
            show: true
          },
          axisBorder: {
            color: colors.gridBorder,
          },
          axisTicks: {
            color: colors.gridBorder,
          },
          crosshairs: {
            stroke: {
              color: colors.secondary,
            },
          },
        },
        yaxis: {
          title: {
            text: 'Revenue ( $1000 x )',
            style:{
              size: 9,
              color: colors.secondary
            }
          },
          tickAmount: 4,
          tooltip: {
            enabled: true
          },
          crosshairs: {
            stroke: {
              color: colors.secondary,
            },
          },
        },
        markers: {
          size: 0,
        },
        stroke: {
          width: 2,
          curve: "straight",
        },
      };
      const revenueChart = new ApexCharts(revenueChartElement, revenueChartOptions);
      revenueChart.render();
    }
    // Revenue Chart - END





  // Monthly Sales Chart
  const monthlySalesChartElement = document.querySelector('#monthlySalesChart');
  if (monthlySalesChartElement) {
    const monthlySalesChartOptions = {
      chart: {
        type: 'bar',
        height: '318',
        parentHeightOffset: 0,
        foreColor: colors.secondary,
        toolbar: {
          show: false
        },
      },
      theme: {
        mode: 'light'
      },
      tooltip: {
        theme: 'light'
      },
      colors: [colors.primary],  
      fill: {
        opacity: .9
      } , 
      grid: {
        padding: {
          bottom: -4
        },
        borderColor: colors.gridBorder,
        xaxis: {
          lines: {
            show: true
          }
        }
      },
      series: [{
        name: 'Sales',
        data: [152,109,93,113,126,161,188,143,102,113,116,124]
      }],
      xaxis: {
        type: 'datetime',
        categories: ['01/01/2024','02/01/2024','03/01/2024','04/01/2024','05/01/2024','06/01/2024','07/01/2024', '08/01/2024','09/01/2024','10/01/2024', '11/01/2024', '12/01/2024'],
        axisBorder: {
          color: colors.gridBorder,
        },
        axisTicks: {
          color: colors.gridBorder,
        },
      },
      yaxis: {
        title: {
          text: 'Number of Sales',
          style:{
            size: 9,
            color: colors.secondary
          }
        },
      },
      legend: {
        show: true,
        position: "top",
        horizontalAlign: 'center',
        fontFamily: fontFamily,
        itemMargin: {
          horizontal: 8,
          vertical: 0
        },
      },
      stroke: {
        width: 0
      },
      dataLabels: {
        enabled: true,
        style: {
          fontSize: '10px',
          fontFamily: fontFamily,
        },
        offsetY: -27
      },
      plotOptions: {
        bar: {
          columnWidth: "50%",
          borderRadius: 4,
          dataLabels: {
            position: 'top',
            orientation: 'vertical',
          }
        },
      },
    }
    
    const monthlySalesChart = new ApexCharts(monthlySalesChartElement, monthlySalesChartOptions);
    monthlySalesChart.render();
  }
  // Monthly Sales Chart - END





  // Cloud Storage Chart
  const storageChartElement = document.querySelector('#storageChart');
  if (storageChartElement) {
    const storageChartOptions = {
      chart: {
        height: 260,
        type: "radialBar"
      },
      series: [67],
      colors: [colors.primary],
      plotOptions: {
        radialBar: {
          hollow: {
            margin: 15,
            size: "70%"
          },
          track: {
            show: true,
            background: colors.gridBorder,
            strokeWidth: '100%',
            opacity: 1,
            margin: 5, 
          },
          dataLabels: {
            showOn: "always",
            name: {
              offsetY: -11,
              show: true,
              color: colors.secondary,
              fontSize: "13px"
            },
            value: {
              color: colors.secondary,
              fontSize: "30px",
              show: true
            }
          }
        }
      },
      fill: {
        opacity: 1
      },
      stroke: {
        lineCap: "round",
      },
      labels: ["Storage Used"]
    };
    
    const storageChart = new ApexCharts(storageChartElement, storageChartOptions);
    storageChart.render();    
  }
  // Cloud Storage Chart - END


})();