package com.kariana.quran.app

import android.annotation.SuppressLint
import android.content.ActivityNotFoundException
import android.content.Context
import android.content.Intent
import android.graphics.Bitmap
import android.net.ConnectivityManager
import android.net.NetworkCapabilities
import android.net.Uri
import android.os.Bundle
import android.view.View
import android.webkit.ValueCallback
import android.webkit.WebChromeClient
import android.webkit.WebResourceError
import android.webkit.WebResourceRequest
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import android.widget.Toast
import androidx.activity.OnBackPressedCallback
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AppCompatActivity
import androidx.core.splashscreen.SplashScreen.Companion.installSplashScreen
import com.kariana.quran.app.databinding.ActivityMainBinding

/**
 * Kariana Quran Official Android Application Shell
 * Engineered for District Directors, Teachers, Admins & Students
 */
class MainActivity : AppCompatActivity() {

    private lateinit var binding: ActivityMainBinding
    private var fileUploadCallback: ValueCallback<Array<Uri>>? = null
    private var backPressedTime: Long = 0

    // Standard Kariana Endpoints
    companion object {
        const val PRIMARY_URL = "https://project.rasel.cloud/kariana/"
        const val BACKUP_URL = "http://192.168.0.100:8015/"
        const val USER_AGENT_SUFFIX = " KarianaQuranAndroidApp/1.0.0"
    }

    private val fileChooserLauncher = registerForActivityResult(
        ActivityResultContracts.StartActivityForResult()
    ) { result ->
        if (result.resultCode == RESULT_OK) {
            val data = result.data
            val results: Array<Uri>? = when {
                data?.dataString != null -> arrayOf(Uri.parse(data.dataString))
                data?.clipData != null -> {
                    val count = data.clipData!!.itemCount
                    Array(count) { i -> data.clipData!!.getItemAt(i).uri }
                }
                else -> null
            }
            fileUploadCallback?.onReceiveValue(results)
        } else {
            fileUploadCallback?.onReceiveValue(null)
        }
        fileUploadCallback = null
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        // Install modern Android 12+ Splash Screen
        installSplashScreen()
        super.onCreate(savedInstanceState)

        binding = ActivityMainBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupBackNavigation()
        setupSwipeRefresh()
        setupRetryButton()
        initWebView()

        loadInitialPage()
    }

    @SuppressLint("SetJavaScriptEnabled")
    private fun initWebView() {
        val settings = binding.webView.settings

        // Enable HTML5, Alpine.js, Tailwind and Modern Web Features
        settings.javaScriptEnabled = true
        settings.domStorageEnabled = true
        settings.databaseEnabled = true
        settings.allowFileAccess = true
        settings.allowContentAccess = true
        settings.useWideViewPort = true
        settings.loadWithOverviewMode = true
        settings.setSupportZoom(false)
        settings.builtInZoomControls = false
        settings.displayZoomControls = false

        // Cache & Performance Optimization
        settings.cacheMode = if (isNetworkAvailable()) {
            WebSettings.LOAD_DEFAULT
        } else {
            WebSettings.LOAD_CACHE_ELSE_NETWORK
        }

        // Custom User Agent Header
        settings.userAgentString = settings.userAgentString + USER_AGENT_SUFFIX

        // Client for Intercepting URLs, WhatsApp, Tel, and Error Handling
        binding.webView.webViewClient = object : WebViewClient() {
            override fun shouldOverrideUrlLoading(
                view: WebView?,
                request: WebResourceRequest?
            ): Boolean {
                val url = request?.url?.toString() ?: return false

                // 1. Native WhatsApp Handling (wa.me, whatsapp://)
                if (url.startsWith("whatsapp://") || 
                    url.startsWith("https://wa.me/") || 
                    url.contains("api.whatsapp.com/send")
                ) {
                    return launchExternalApp(url, "WhatsApp অ্যাপ্লিকেশনটি ইনস্টল করা নেই")
                }

                // 2. Native Phone Dialer Handling (tel:)
                if (url.startsWith("tel:")) {
                    val dialIntent = Intent(Intent.ACTION_DIAL, Uri.parse(url))
                    startActivity(dialIntent)
                    return true
                }

                // 3. Native Email Handling (mailto:)
                if (url.startsWith("mailto:")) {
                    return launchExternalApp(url, "ইমেইল অ্যাপ্লিকেশন পাওয়া যায়নি")
                }

                // 4. In-App Navigation for Kariana Host
                val host = Uri.parse(url).host
                if (host != null && (host.contains("project.rasel.cloud") || host.contains("104.207.93.68") || host.contains("192.168.0.100") || host.contains("localhost") || host.contains("trycloudflare.com"))) {
                    return false
                }

                // 5. External Web Links (Open in default browser)
                return launchExternalApp(url, null)
            }

            override fun onPageStarted(view: WebView?, url: String?, favicon: Bitmap?) {
                super.onPageStarted(view, url, favicon)
                binding.progressBar.visibility = View.VISIBLE
                binding.layoutOffline.visibility = View.GONE
            }

            override fun onPageFinished(view: WebView?, url: String?) {
                super.onPageFinished(view, url)
                binding.progressBar.visibility = View.GONE
                binding.swipeRefreshLayout.isRefreshing = false
            }

            override fun onReceivedError(
                view: WebView?,
                request: WebResourceRequest?,
                error: WebResourceError?
            ) {
                super.onReceivedError(view, request, error)
                if (request?.isForMainFrame == true) {
                    binding.progressBar.visibility = View.GONE
                    binding.swipeRefreshLayout.isRefreshing = false
                    if (!isNetworkAvailable()) {
                        binding.layoutOffline.visibility = View.VISIBLE
                    }
                }
            }
        }

        // ChromeClient for File Uploads and Progress Bar
        binding.webView.webChromeClient = object : WebChromeClient() {
            override fun onProgressChanged(view: WebView?, newProgress: Int) {
                super.onProgressChanged(view, newProgress)
                binding.progressBar.progress = newProgress
                if (newProgress == 100) {
                    binding.progressBar.visibility = View.GONE
                }
            }

            override fun onShowFileChooser(
                webView: WebView?,
                filePathCallback: ValueCallback<Array<Uri>>?,
                fileChooserParams: FileChooserParams?
            ): Boolean {
                fileUploadCallback?.onReceiveValue(null)
                fileUploadCallback = filePathCallback

                val intent = fileChooserParams?.createIntent() ?: Intent(Intent.ACTION_GET_CONTENT).apply {
                    type = "*/*"
                    addCategory(Intent.CATEGORY_OPENABLE)
                }

                try {
                    fileChooserLauncher.launch(intent)
                } catch (e: ActivityNotFoundException) {
                    fileUploadCallback = null
                    Toast.makeText(this@MainActivity, "ফাইল সিলেক্টর পাওয়া যায়নি", Toast.LENGTH_SHORT).show()
                    return false
                }
                return true
            }
        }
    }

    private fun loadInitialPage() {
        if (isNetworkAvailable()) {
            binding.layoutOffline.visibility = View.GONE
            binding.webView.loadUrl(PRIMARY_URL)
        } else {
            binding.layoutOffline.visibility = View.VISIBLE
        }
    }

    private fun setupSwipeRefresh() {
        binding.swipeRefreshLayout.setColorSchemeResources(
            R.color.gold_rich,
            R.color.emerald_vibrant
        )
        binding.swipeRefreshLayout.setOnRefreshListener {
            if (isNetworkAvailable()) {
                binding.webView.reload()
            } else {
                binding.swipeRefreshLayout.isRefreshing = false
                binding.layoutOffline.visibility = View.VISIBLE
            }
        }
    }

    private fun setupRetryButton() {
        binding.btnRetry.setOnClickListener {
            if (isNetworkAvailable()) {
                binding.layoutOffline.visibility = View.GONE
                binding.webView.reload()
            } else {
                Toast.makeText(this, R.string.no_internet, Toast.LENGTH_SHORT).show()
            }
        }
    }

    private fun setupBackNavigation() {
        onBackPressedDispatcher.addCallback(this, object : OnBackPressedCallback(true) {
            override fun handleOnBackPressed() {
                if (binding.webView.canGoBack()) {
                    binding.webView.goBack()
                } else {
                    if (System.currentTimeMillis() - backPressedTime < 2000) {
                        finish()
                    } else {
                        backPressedTime = System.currentTimeMillis()
                        Toast.makeText(this@MainActivity, R.string.exit_confirm, Toast.LENGTH_SHORT).show()
                    }
                }
            }
        })
    }

    private fun launchExternalApp(url: String, errorMessage: String?): Boolean {
        try {
            val intent = Intent(Intent.ACTION_VIEW, Uri.parse(url))
            startActivity(intent)
            return true
        } catch (e: ActivityNotFoundException) {
            if (errorMessage != null) {
                Toast.makeText(this, errorMessage, Toast.LENGTH_SHORT).show()
            }
            return false
        }
    }

    private fun isNetworkAvailable(): Boolean {
        val cm = getSystemService(Context.CONNECTIVITY_SERVICE) as ConnectivityManager
        val network = cm.activeNetwork ?: return false
        val caps = cm.getNetworkCapabilities(network) ?: return false
        return caps.hasCapability(NetworkCapabilities.NET_CAPABILITY_INTERNET)
    }

    override fun onDestroy() {
        binding.webView.destroy()
        super.onDestroy()
    }
}
