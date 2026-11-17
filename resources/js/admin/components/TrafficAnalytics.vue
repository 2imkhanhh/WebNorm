<template>
  <div class="traffic-analytics-stack">
    <div class="traffic-main-grid">
      <div class="analytics-card trend-card">
        <div class="analytics-card-header">
          <div>
            <div class="analytics-title-row">
              <h3 class="analytics-title">Lưu lượng truy cập website</h3>
            </div>
            <div class="analytics-kpi-inline">
              <div class="inline-kpi-item">
                <span class="legend-swatch views"></span>
                <span class="kpi-label">Lượt xem trang:</span>
                <strong class="kpi-val">{{ formatNumber(periodTotals.views) }}</strong>
              </div>
              <div class="inline-kpi-item">
                <span class="legend-swatch visitors"></span>
                <span class="kpi-label">Khách truy cập:</span>
                <strong class="kpi-val">{{ formatNumber(periodTotals.visitors) }}</strong>
              </div>
            </div>
          </div>

          <div class="range-switcher" role="group" aria-label="Chọn khoảng thời gian">
            <button v-for="opt in rangeOptions" :key="opt.key" type="button" class="range-btn"
              :class="{ active: activeRange === opt.key }" @click="activeRange = opt.key; hoverIndex = null">
              {{ opt.label }}
            </button>
          </div>
        </div>

        <div class="chart-canvas-wrap" @mouseleave="hoverIndex = null">
          <svg class="traffic-svg-chart" viewBox="0 0 720 250" preserveAspectRatio="none" role="img"
            aria-label="Biểu đồ lượt xem và khách truy cập">
            <defs>
              <linearGradient id="normViewsGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#0f172a" stop-opacity="0.16" />
                <stop offset="100%" stop-color="#0f172a" stop-opacity="0.0" />
              </linearGradient>
              <linearGradient id="normVisitorsGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#10b981" stop-opacity="0.22" />
                <stop offset="100%" stop-color="#10b981" stop-opacity="0.0" />
              </linearGradient>
            </defs>

            <!-- Horizontal Gridlines & Y Ticks -->
            <g v-for="tick in yTicks" :key="tick.y">
              <line :x1="padLeft" :x2="svgWidth - padRight" :y1="tick.y" :y2="tick.y" stroke="#e2e8f0"
                stroke-dasharray="4 4" stroke-width="1" />
              <text :x="padLeft - 10" :y="tick.y + 4" text-anchor="end" class="svg-axis-label">
                {{ tick.label }}
              </text>
            </g>

            <!-- Area Fills -->
            <path v-if="viewsAreaPath" :d="viewsAreaPath" fill="url(#normViewsGrad)" />
            <path v-if="visitorsAreaPath" :d="visitorsAreaPath" fill="url(#normVisitorsGrad)" />

            <!-- Trend Lines -->
            <path v-if="viewsLinePath" :d="viewsLinePath" fill="none" stroke="#0f172a" stroke-width="2.5"
              stroke-linecap="round" stroke-linejoin="round" />
            <path v-if="visitorsLinePath" :d="visitorsLinePath" fill="none" stroke="#10b981" stroke-width="2.25"
              stroke-linecap="round" stroke-linejoin="round" />

            <!-- Crosshair on hover -->
            <line v-if="hoveredPoint" :x1="hoveredPoint.x" :x2="hoveredPoint.x" :y1="padTop" :y2="padTop + plotHeight"
              stroke="#94a3b8" stroke-width="1" stroke-dasharray="3 3" />

            <!-- Data dots (sparse points or hovered point) -->
            <g v-for="(pt, idx) in chartPoints" :key="idx">
              <circle v-if="showPointDot(idx)" :cx="pt.x" :cy="pt.yViews" :r="hoverIndex === idx ? 5 : 3" fill="#0f172a"
                stroke="#ffffff" stroke-width="2" />
              <circle v-if="showPointDot(idx)" :cx="pt.x" :cy="pt.yVisitors" :r="hoverIndex === idx ? 5 : 3"
                fill="#10b981" stroke="#ffffff" stroke-width="2" />
            </g>

            <!-- X Axis Labels -->
            <text v-for="(pt, idx) in chartPoints" :key="'xlabel-' + idx" v-show="shouldShowXLabel(idx)" :x="pt.x"
              :y="svgHeight - 10" text-anchor="middle" class="svg-axis-label">
              {{ pt.label }}
            </text>

            <!-- Invisible Hover Hit Columns -->
            <rect v-for="(pt, idx) in chartPoints" :key="'hit-' + idx" :x="pt.hitX" :y="padTop" :width="pt.hitWidth"
              :height="plotHeight" fill="transparent" class="svg-hit-col" @mouseenter="hoverIndex = idx" />
          </svg>

          <!-- Floating Hover Tooltip -->
          <div v-if="hoveredPoint" class="chart-tooltip" :style="{
            left: tooltipLeftPercent + '%',
            transform: tooltipAlignTransform,
          }">
            <div class="tooltip-date">{{ hoveredPoint.full_date }}</div>
            <div class="tooltip-row">
              <span class="legend-swatch views"></span>
              <span>Lượt xem trang:</span>
              <strong>{{ formatNumber(hoveredPoint.views) }}</strong>
            </div>
            <div class="tooltip-row">
              <span class="legend-swatch visitors"></span>
              <span>Khách truy cập:</span>
              <strong>{{ formatNumber(hoveredPoint.visitors) }}</strong>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Language & Device Donut Breakdowns -->
      <div class="analytics-card breakdown-card">
        <div class="analytics-card-header compact">
          <div>
            <h3 class="analytics-title">Cơ cấu truy cập</h3>
            <p class="analytics-subtitle">Tổng hợp 30 ngày gần nhất</p>
          </div>
        </div>

        <div class="donut-blocks">
          <!-- Language Donut -->
          <div class="donut-item">
            <div class="donut-svg-wrap">
              <svg viewBox="0 0 88 88" class="donut-svg">
                <circle cx="44" cy="44" r="34" fill="none" stroke="#f1f5f9" stroke-width="10" />
                <circle v-if="langStats.total > 0" cx="44" cy="44" r="34" fill="none" stroke="#0f172a" stroke-width="10"
                  stroke-linecap="butt" :stroke-dasharray="`${langStats.viDash} ${circumference}`"
                  transform="rotate(-90 44 44)" />
                <circle v-if="langStats.en > 0" cx="44" cy="44" r="34" fill="none" stroke="#eab308" stroke-width="10"
                  stroke-linecap="butt" :stroke-dasharray="`${langStats.enDash} ${circumference}`"
                  :stroke-dashoffset="-langStats.viDash" transform="rotate(-90 44 44)" />
                <text x="44" y="42" text-anchor="middle" class="donut-center-num">
                  {{ langStats.total > 0 ? langStats.viPct + '%' : '—' }}
                </text>
                <text x="44" y="55" text-anchor="middle" class="donut-center-sub">VI</text>
              </svg>
            </div>
            <div class="donut-meta">
              <div class="donut-heading">Ngôn ngữ xem trang</div>
              <div class="donut-legend-row">
                <span class="legend-dot dark"></span>
                <span class="legend-name">Tiếng Việt</span>
                <strong class="legend-val">{{ formatNumber(langStats.vi) }} ({{ langStats.viPct }}%)</strong>
              </div>
              <div class="donut-legend-row">
                <span class="legend-dot gold"></span>
                <span class="legend-name">Tiếng Anh</span>
                <strong class="legend-val">{{ formatNumber(langStats.en) }} ({{ langStats.enPct }}%)</strong>
              </div>
            </div>
          </div>

          <div class="donut-divider"></div>

          <!-- Device Donut -->
          <div class="donut-item">
            <div class="donut-svg-wrap">
              <svg viewBox="0 0 88 88" class="donut-svg">
                <circle cx="44" cy="44" r="34" fill="none" stroke="#f1f5f9" stroke-width="10" />
                <circle v-if="deviceStats.total > 0" cx="44" cy="44" r="34" fill="none" stroke="#334155"
                  stroke-width="10" stroke-linecap="butt"
                  :stroke-dasharray="`${deviceStats.desktopDash} ${circumference}`" transform="rotate(-90 44 44)" />
                <circle v-if="deviceStats.mobile > 0" cx="44" cy="44" r="34" fill="none" stroke="#10b981"
                  stroke-width="10" stroke-linecap="butt"
                  :stroke-dasharray="`${deviceStats.mobileDash} ${circumference}`"
                  :stroke-dashoffset="-deviceStats.desktopDash" transform="rotate(-90 44 44)" />
                <text x="44" y="42" text-anchor="middle" class="donut-center-num">
                  {{ deviceStats.total > 0 ? deviceStats.desktopPct + '%' : '—' }}
                </text>
                <text x="44" y="55" text-anchor="middle" class="donut-center-sub">PC</text>
              </svg>
            </div>
            <div class="donut-meta">
              <div class="donut-heading">Thiết bị truy cập</div>
              <div class="donut-legend-row">
                <span class="legend-dot slate"></span>
                <span class="legend-name">Máy tính</span>
                <strong class="legend-val">{{ formatNumber(deviceStats.desktop) }} ({{ deviceStats.desktopPct
                  }}%)</strong>
              </div>
              <div class="donut-legend-row">
                <span class="legend-dot emerald"></span>
                <span class="legend-name">Điện thoại</span>
                <strong class="legend-val">{{ formatNumber(deviceStats.mobile) }} ({{ deviceStats.mobilePct
                  }}%)</strong>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  traffic: {
    type: Object,
    default: () => ({}),
  },
});

const rangeOptions = [
  { key: '7d', label: '7 ngày' },
  { key: '30d', label: '30 ngày' },
  { key: '6m', label: '6 tháng' },
];

const activeRange = ref('7d');
const hoverIndex = ref(null);

const summary30d = computed(() => props.traffic?.summary_30d || {
  views: 0,
  visitors: 0,
  vi_views: 0,
  en_views: 0,
  desktop_views: 0,
  mobile_views: 0,
});

const activeSeries = computed(() => {
  const list = props.traffic?.series?.[activeRange.value];
  return Array.isArray(list) ? list : [];
});

const periodTotals = computed(() => {
  return activeSeries.value.reduce(
    (acc, item) => {
      acc.views += Number(item.views || 0);
      acc.visitors += Number(item.visitors || 0);
      return acc;
    },
    { views: 0, visitors: 0 }
  );
});

// SVG Geometry
const svgWidth = 720;
const svgHeight = 250;
const padLeft = 46;
const padRight = 18;
const padTop = 18;
const padBottom = 34;
const plotWidth = svgWidth - padLeft - padRight;
const plotHeight = svgHeight - padTop - padBottom;

const maxValue = computed(() => {
  let maxVal = 0;
  for (const item of activeSeries.value) {
    if (item.views > maxVal) maxVal = item.views;
    if (item.visitors > maxVal) maxVal = item.visitors;
  }
  if (maxVal <= 4) return 4;
  const magnitude = Math.pow(10, Math.floor(Math.log10(maxVal)));
  const normalized = maxVal / magnitude;
  let niceTop = Math.ceil(normalized * 2) / 2;
  if (niceTop * magnitude < maxVal) niceTop += 0.5;
  return Math.max(4, Math.ceil((niceTop * magnitude) / 4) * 4);
});

const yTicks = computed(() => {
  const steps = 4;
  const max = maxValue.value;
  const ticks = [];
  for (let i = 0; i <= steps; i++) {
    const val = Math.round((max / steps) * (steps - i));
    const y = padTop + (plotHeight / steps) * i;
    ticks.push({
      y,
      label: formatCompact(val),
    });
  }
  return ticks;
});

const chartPoints = computed(() => {
  const items = activeSeries.value;
  const count = items.length;
  if (count === 0) return [];

  const max = maxValue.value;
  const stepX = count > 1 ? plotWidth / (count - 1) : 0;

  return items.map((item, idx) => {
    const x = count === 1 ? padLeft + plotWidth / 2 : padLeft + idx * stepX;
    const yViews = padTop + plotHeight - (Number(item.views || 0) / max) * plotHeight;
    const yVisitors = padTop + plotHeight - (Number(item.visitors || 0) / max) * plotHeight;
    const halfStep = count > 1 ? stepX / 2 : plotWidth / 2;

    return {
      ...item,
      x,
      yViews,
      yVisitors,
      hitX: Math.max(padLeft, x - halfStep),
      hitWidth: count > 1 ? (idx === 0 || idx === count - 1 ? halfStep : stepX) : plotWidth,
    };
  });
});

// Build smooth monotone-ish bezier SVG path
const buildSmoothPath = (pts, key) => {
  if (!pts.length) return '';
  if (pts.length === 1) return `M ${pts[0].x} ${pts[0][key]}`;

  let d = `M ${pts[0].x.toFixed(2)} ${pts[0][key].toFixed(2)}`;
  for (let i = 0; i < pts.length - 1; i++) {
    const p0 = pts[i];
    const p1 = pts[i + 1];
    const cpX = (p0.x + p1.x) / 2;
    d += ` C ${cpX.toFixed(2)} ${p0[key].toFixed(2)}, ${cpX.toFixed(2)} ${p1[key].toFixed(2)}, ${p1.x.toFixed(2)} ${p1[key].toFixed(2)}`;
  }
  return d;
};

const viewsLinePath = computed(() => buildSmoothPath(chartPoints.value, 'yViews'));
const visitorsLinePath = computed(() => buildSmoothPath(chartPoints.value, 'yVisitors'));

const buildAreaPath = (linePath, pts) => {
  if (!linePath || !pts.length) return '';
  const baseY = (padTop + plotHeight).toFixed(2);
  const firstX = pts[0].x.toFixed(2);
  const lastX = pts[pts.length - 1].x.toFixed(2);
  return `${linePath} L ${lastX} ${baseY} L ${firstX} ${baseY} Z`;
};

const viewsAreaPath = computed(() => buildAreaPath(viewsLinePath.value, chartPoints.value));
const visitorsAreaPath = computed(() => buildAreaPath(visitorsLinePath.value, chartPoints.value));

const showPointDot = (idx) => {
  if (hoverIndex.value === idx) return true;
  return activeRange.value !== '30d';
};

const shouldShowXLabel = (idx) => {
  const total = chartPoints.value.length;
  if (total <= 8) return true;
  return idx === 0 || idx === total - 1 || idx % 5 === 0;
};

const hoveredPoint = computed(() => {
  if (hoverIndex.value === null) return null;
  return chartPoints.value[hoverIndex.value] || null;
});

const tooltipLeftPercent = computed(() => {
  if (!hoveredPoint.value) return 0;
  return (hoveredPoint.value.x / svgWidth) * 100;
});

const tooltipAlignTransform = computed(() => {
  const pct = tooltipLeftPercent.value;
  if (pct < 20) return 'translateX(0%)';
  if (pct > 80) return 'translateX(-100%)';
  return 'translateX(-50%)';
});

// Donut calculations
const radius = 34;
const circumference = 2 * Math.PI * radius;

const langStats = computed(() => {
  const vi = Number(summary30d.value.vi_views || 0);
  const en = Number(summary30d.value.en_views || 0);
  const total = vi + en;
  if (total === 0) {
    return { vi: 0, en: 0, total: 0, viPct: 0, enPct: 0, viDash: 0, enDash: 0 };
  }
  const viPct = Math.round((vi / total) * 100);
  const enPct = 100 - viPct;
  return {
    vi,
    en,
    total,
    viPct,
    enPct,
    viDash: (vi / total) * circumference,
    enDash: (en / total) * circumference,
  };
});

const deviceStats = computed(() => {
  const desktop = Number(summary30d.value.desktop_views || 0);
  const mobile = Number(summary30d.value.mobile_views || 0);
  const total = desktop + mobile;
  if (total === 0) {
    return { desktop: 0, mobile: 0, total: 0, desktopPct: 0, mobilePct: 0, desktopDash: 0, mobileDash: 0 };
  }
  const desktopPct = Math.round((desktop / total) * 100);
  const mobilePct = 100 - desktopPct;
  return {
    desktop,
    mobile,
    total,
    desktopPct,
    mobilePct,
    desktopDash: (desktop / total) * circumference,
    mobileDash: (mobile / total) * circumference,
  };
});

const formatNumber = (num) => {
  return Number(num || 0).toLocaleString('vi-VN');
};

const formatCompact = (num) => {
  if (num >= 1000000) return (num / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
  if (num >= 1000) return (num / 1000).toFixed(1).replace(/\.0$/, '') + 'k';
  return String(num);
};
</script>
