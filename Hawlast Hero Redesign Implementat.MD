# Hawlast Hero Redesign Implementation Guide

## 1. Rationale
This refactor converts the Hawlast hero section into a modern, 2-column SaaS layout inspired by Pathmonk while maintaining Hawlast's core brand elements (Orange `#ff6600`, Navy `#1a1a1a`/`#1a2a6c`, and Inter typography). By transitioning from a stacked, search-only header to a value-driven hero section, Hawlast clearly presents its full scope of services—domains, hosting, AI chatbots, automation, and software integrations—right at the top of the fold before directing users to action.

---

## 2. Implementation Code

### Hero Block Partial (`partials/hero.php` or `index.php`)

```php
<!-- HERO SECTION: PATHMONK PATTERN FOR HAWLAST -->
<header class="relative bg-slate-50 pt-12 pb-20 md:pt-20 md:pb-28 overflow-hidden border-b border-slate-200/80">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
      
      <!-- Left Column: Copy & CTAs -->
      <div class="lg:col-span-7 text-left space-y-6">
        
        <!-- H1 Headline -->
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-[1.15]">
          Launch and automate your business <span class="text-[#ff6600]">in one place</span>
        </h1>

        <!-- Subheadline -->
        <p class="text-lg sm:text-xl text-slate-600 leading-relaxed max-w-2xl font-normal">
          Get fast web hosting, custom domains, AI chatbots, and seamless business software integration. We automate your growth stack so you can focus on building your brand.
        </p>

        <!-- Primary & Secondary CTAs -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 pt-2">
          <a href="#plans" class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white bg-[#ff6600] hover:bg-[#e05500] rounded-full shadow-md hover:shadow-lg transition-all duration-200">
            Start for Free
          </a>
          <a href="contact.php" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold text-slate-800 bg-white border-2 border-slate-300 hover:border-[#ff6600] hover:text-[#ff6600] rounded-full transition-all duration-200">
            Book a Demo
          </a>
        </div>

        <!-- Trust / Proof Line -->
        <div class="pt-4 flex items-center gap-2 text-sm text-slate-500 border-t border-slate-200/60 max-w-md">
          <i class="fa-solid fa-circle-check text-[#ff6600]"></i>
          <span>Trusted by startups, SMEs, and agencies across Kenya for over 15 years.</span>
        </div>

      </div>

      <!-- Right Column: Product Ecosystem Visual -->
      <div class="lg:col-span-5 relative">
        <div class="relative mx-auto w-full max-w-lg lg:max-w-none bg-white p-6 rounded-2xl shadow-2xl border border-slate-100 space-y-4">
          
          <!-- Domain & Hosting Node -->
          <div class="flex items-center gap-4 p-3.5 bg-slate-50 rounded-xl border border-slate-200/60">
            <div class="p-3 bg-orange-100 text-[#ff6600] rounded-lg">
              <i class="fa-solid fa-globe text-xl"></i>
            </div>
            <div>
              <h4 class="font-bold text-slate-900 text-sm">Domain & Cloud Hosting</h4>
              <p class="text-xs text-slate-500">Fast NVMe Servers + Custom .KE / .COM</p>
            </div>
          </div>

          <!-- AI Chatbots Node -->
          <div class="flex items-center gap-4 p-3.5 bg-slate-50 rounded-xl border border-slate-200/60 ml-4">
            <div class="p-3 bg-blue-100 text-blue-600 rounded-lg">
              <i class="fa-solid fa-robot text-xl"></i>
            </div>
            <div>
              <h4 class="font-bold text-slate-900 text-sm">AI Chatbots</h4>
              <p class="text-xs text-slate-500">24/7 Automated Support & Lead Capture</p>
            </div>
          </div>

          <!-- Business Integration Node -->
          <div class="flex items-center gap-4 p-3.5 bg-slate-50 rounded-xl border border-slate-200/60 ml-8">
            <div class="p-3 bg-emerald-100 text-emerald-600 rounded-lg">
              <i class="fa-solid fa-diagram-project text-xl"></i>
            </div>
            <div>
              <h4 class="font-bold text-slate-900 text-sm">Software Integration</h4>
              <p class="text-xs text-slate-500">M-Pesa, CRMs & Workflow Automation</p>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</header>