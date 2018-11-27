# http2priorities
Test Page for HTTP/2 prioritization (server and browser)

Server support for HTTP/2 prioritization requires getting a lot of things right (more details in the [Cloudflare blog post](https://blog.cloudflare.com/http-2-prioritization-with-nginx/)).

This is a test page for exercising HTTP/2 prioritization (particularly for Chrome). It is structured in such a way that the preload scanner will discover low-priority images that are displayed below the viewport and start loading them before 4 high-priority resources are discovered:

1. The custom font used for the page (not discovered until layout).
1. The background "pebble" image used for the page background (not discovered until layout).
1. The image of the car visible in the viewport (injected by javascript at the top of the page).
1. A blocking script at the end of the document (injected by document.write).

The text near the top of the page will also change after the last blocking script is loaded to indicate that DOMContentLoaded has fired.

The test should work well on all modern browsers but it depends on browsers doing something intelligent with priorities. It was crafted specifically to exercise Chrome's prioritization and expose server prioritization issues.

To test, I HIGHLY recommend running the testing with a slow network connection (1.6 Mbps 3G should be fine). That will make any issues glaringly obvious. In case it wasn't obvious, you will need to deploy this test page on your server to use it as a test target and make sure it is HTTPS (otherwise HTTP/2 will not be used). DO NOT use Chrome Dev Tools' built-in connection profiles. The traffic shaping for those are done internal to the browser and introduce different buffers that lead to incorrect results. There are [good network-level options available on most platforms](https://calendar.perfplanet.com/2016/testing-with-realistic-networking-conditions/).

When it is working well you will see the important resources at the end of the waterfall completing quickly.

**IMPORTANT:** All Four high-priority requests need to complete early. It is not uncommon for one or two of them to complete early but bottom.js and visible.jpg get delayed. That is a symptom of a server that supports prioritization but with too much output buffering (it saw the font request before it had sent enough data to fill the buffers).

![Waterfall with important resources completing late.](docs/waterfall_good.png)

When it is not working well you will see them delayed significantly:

![Waterfall with important resources completing late.](docs/waterfall_bad.png)

The visual experience is also significantly faster when working correctly:

![Filmstrip](docs/filmstrip.png)

As of Chrome 70, the HTTP/2 dependency/priority tree for the page looks like this (all requests are marked as "exclusive"):

![Priority tree](docs/priorities.png)
