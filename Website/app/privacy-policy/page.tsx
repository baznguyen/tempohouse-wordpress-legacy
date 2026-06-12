'use client';

import { useState } from 'react';
import Link from 'next/link';
import styles from './page.module.css';

type Lang = 'en' | 'vi';

export default function PrivacyPolicyPage() {
  const [lang, setLang] = useState<Lang>('en');

  return (
    <main className={styles.page}>
      <div className={styles.inner}>
        <Link href="/" className={styles.back}>
          <span className={styles.backArrow}>←</span> TEMPO House
        </Link>

        <header className={styles.header}>
          <p className={styles.eyebrow}>Legal</p>
          <h1 className={styles.title}>
            {lang === 'en' ? 'Privacy Policy' : 'Chính Sách Bảo Mật'}
          </h1>
          <p className={styles.meta}>
            {lang === 'en'
              ? 'Last updated: May 2026 · Effective: 1 January 2026'
              : 'Cập nhật: Tháng 5 năm 2026 · Hiệu lực: 1 tháng 1 năm 2026'}
          </p>
        </header>

        <div className={styles.langToggle}>
          <button
            className={`${styles.langBtn} ${lang === 'en' ? styles.langBtnActive : ''}`}
            onClick={() => setLang('en')}
          >
            English
          </button>
          <button
            className={`${styles.langBtn} ${lang === 'vi' ? styles.langBtnActive : ''}`}
            onClick={() => setLang('vi')}
          >
            Tiếng Việt
          </button>
        </div>

        {lang === 'en' ? <EnglishContent /> : <VietnameseContent />}

        <footer className={styles.footer}>
          <span>© 2026 TEMPO House. All rights reserved.</span>
          <span>
            {lang === 'en' ? 'Governed by the laws of Vietnam.' : 'Điều chỉnh bởi pháp luật Việt Nam.'}
          </span>
        </footer>
      </div>
    </main>
  );
}

function EnglishContent() {
  return (
    <div className={styles.content}>

      <h2>1. Who We Are</h2>
      <p>
        TEMPO House ("we", "us", "our") operates a specialty café, cocktail bar, art gallery, and
        events space located in Ho Chi Minh City, Vietnam, and the website at{' '}
        <a href="https://tempohouse.com.vn">tempohouse.com.vn</a>.
      </p>
      <div className={styles.contactBlock}>
        <p><strong>Data Controller</strong></p>
        <p>TEMPO House · Ho Chi Minh City, Vietnam</p>
        <p>Data protection enquiries: <a href="mailto:info@tempohouse.com.vn">info@tempohouse.com.vn</a></p>
      </div>
      <p>
        This policy is governed by the <strong>Law on Personal Data Protection, Law No. 91/2025/QH15</strong>{' '}
        (effective 1 January 2026), <strong>Decree No. 356/2025/NĐ-CP</strong>, the Law on Cybersecurity
        No. 24/2018/QH14, the Law on Protection of Consumers&apos; Rights No. 19/2023/QH15, and
        Decree 91/2020/NĐ-CP on anti-spam.
      </p>

      <h2>2. Data We Collect and Why</h2>

      <h3>2.1 Reservations and Table Bookings</h3>
      <div className={styles.dataCard}>
        <p><strong>What:</strong> Name, phone, email, party size, date/time, special requests or dietary requirements.</p>
        <p><strong>Why:</strong> To confirm and manage your reservation; to contact you about changes or cancellations.</p>
        <p><strong>Legal basis:</strong> Performance of a booking contract.</p>
        <p><strong>Note:</strong> Dietary or health-related requirements constitute <em>sensitive personal data</em> and are collected only with your explicit consent. You may decline without affecting your booking.</p>
      </div>

      <h3>2.2 Event and Private Function Bookings</h3>
      <div className={styles.dataCard}>
        <p><strong>What:</strong> Name, phone, email, company name (if applicable), billing address, event details, headcount, payment information.</p>
        <p><strong>Why:</strong> To plan, confirm, and invoice your event.</p>
        <p><strong>Legal basis:</strong> Performance of contract; payment processing necessary for billing.</p>
        <p><strong>Note:</strong> Financial and payment data is classified as <em>sensitive personal data</em> under Vietnamese law.</p>
      </div>

      <h3>2.3 Email Marketing and Newsletter</h3>
      <div className={styles.dataCard}>
        <p><strong>What:</strong> Name, email address.</p>
        <p><strong>Why:</strong> To send updates, event announcements, promotions, and news about TEMPO House.</p>
        <p><strong>Legal basis:</strong> Your explicit, separate opt-in consent. This is <em>never</em> a condition of making a booking.</p>
        <p><strong>Unsubscribe:</strong> Via the link in any email or by emailing <a href="mailto:info@tempohouse.com.vn">info@tempohouse.com.vn</a> at any time.</p>
        <p><strong>Anti-spam compliance:</strong> Commercial emails are labelled &ldquo;QC&rdquo; per Decree 91/2020/NĐ-CP. We send no more than 3 commercial messages per day.</p>
      </div>

      <h3>2.4 Website Enquiry Form</h3>
      <div className={styles.dataCard}>
        <p><strong>What:</strong> Name, email address, message content.</p>
        <p><strong>Why:</strong> To respond to your enquiry.</p>
        <p><strong>Legal basis:</strong> Your consent at the time of submission.</p>
      </div>

      <h3>2.5 Website Analytics (Google Analytics 4)</h3>
      <div className={styles.dataCard}>
        <p><strong>What:</strong> Anonymised IP address, device type, browser, pages visited, session duration, referral source.</p>
        <p><strong>Why:</strong> To understand how visitors use our website and improve the experience.</p>
        <p><strong>Third-party processor:</strong> Google LLC, USA (Google Analytics 4).</p>
        <p><strong>Cross-border transfer:</strong> Data is transmitted to Google servers in the USA.</p>
        <p><strong>Note:</strong> Website behavioural data is classified as <em>sensitive personal data</em> under Vietnamese law. Analytics tracking activates <em>only after</em> you provide consent via our cookie banner.</p>
      </div>

      <h3>2.6 Meta Pixel (Facebook / Instagram Advertising)</h3>
      <div className={styles.dataCard}>
        <p><strong>What:</strong> Browsing behaviour on our website, linked to your Facebook/Instagram account where applicable.</p>
        <p><strong>Why:</strong> To measure advertising effectiveness and show relevant ads to people who have visited our site.</p>
        <p><strong>Third-party processor:</strong> Meta Platforms, Inc., USA.</p>
        <p><strong>Cross-border transfer:</strong> Data is transmitted to Meta servers in the USA.</p>
        <p><strong>Note:</strong> This tracking activates <em>only after</em> your consent via our cookie banner.</p>
      </div>

      <h3>2.7 CCTV and Security Surveillance</h3>
      <div className={styles.dataCard}>
        <p><strong>What:</strong> Video footage of our venue premises (café, bar, gallery, event spaces, and entry areas).</p>
        <p><strong>Why:</strong> Security, safety, and protection of guests, staff, and property.</p>
        <p><strong>Legal basis:</strong> Legitimate interests in maintaining a safe premises.</p>
        <p><strong>Retention:</strong> Footage is retained for 90 days, then permanently deleted, unless required for an active security or legal matter.</p>
        <p><strong>Access:</strong> Authorised TEMPO House management only, or law enforcement where legally required.</p>
        <p><strong>Notice:</strong> CCTV signage is displayed at all entry points to our venue.</p>
      </div>

      <h3>2.8 Payment Records</h3>
      <div className={styles.dataCard}>
        <p><strong>What:</strong> Transaction amount, date, payment method (card type), reference number. We do <em>not</em> store raw card numbers.</p>
        <p><strong>Why:</strong> Billing, accounting, and legal record-keeping obligations.</p>
        <p><strong>Processor:</strong> Payments are processed by our payment gateway provider(s). Data processing agreements are in place with all payment processors.</p>
        <p><strong>Retention:</strong> 10 years per Vietnamese accounting law (Law No. 88/2015/QH13).</p>
      </div>

      <h3>2.9 Social Media and Direct Enquiries</h3>
      <div className={styles.dataCard}>
        <p><strong>What:</strong> Name, message content, and any information you share when contacting us via Instagram, Facebook, TikTok, or other platforms.</p>
        <p><strong>Why:</strong> To respond to your enquiry.</p>
        <p><strong>Note:</strong> Social media platforms also collect data under their own privacy policies, which we do not control.</p>
      </div>

      <h2>3. How We Share Your Data</h2>
      <p>We <strong>do not sell</strong> your personal data. We share data only with the following parties:</p>

      <div className={styles.tableWrap}>
        <table className={styles.table}>
          <thead>
            <tr>
              <th>Service</th>
              <th>Provider</th>
              <th>Country</th>
              <th>Purpose</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Website analytics</td>
              <td>Google LLC (GA4)</td>
              <td>USA</td>
              <td>Usage analytics</td>
            </tr>
            <tr>
              <td>Advertising pixels</td>
              <td>Meta Platforms, Inc.</td>
              <td>USA</td>
              <td>Ad measurement &amp; remarketing</td>
            </tr>
            <tr>
              <td>Email marketing</td>
              <td>Klaviyo, Inc.</td>
              <td>USA</td>
              <td>Marketing communications</td>
            </tr>
            <tr>
              <td>Payment processing</td>
              <td>Payment gateway provider(s)</td>
              <td>Vietnam / varies</td>
              <td>Secure payment collection</td>
            </tr>
          </tbody>
        </table>
      </div>

      <p>
        We have data processing agreements with all third-party processors, limiting their use of your
        data strictly to the stated purpose.
      </p>
      <p>
        We may also disclose personal data to law enforcement, regulatory authorities, or courts when
        required by Vietnamese law — including the Ministry of Public Security under the Law on
        Cybersecurity — or to protect the safety of persons on our premises.
      </p>

      <h2>4. Cross-Border Data Transfers</h2>
      <p>
        Some of our service providers are based outside Vietnam (primarily in the USA). When personal
        data is transferred internationally, we:
      </p>
      <ul>
        <li>Disclose the transfer to you in advance (as set out in this policy);</li>
        <li>Obtain your explicit consent where required;</li>
        <li>Maintain data transfer agreements with all receiving parties;</li>
        <li>
          Have filed or will file Transfer Impact Assessments (TIA) with the Ministry of Public
          Security (Department A05) as required by Law No. 91/2025/QH15 and Decree 356/2025/NĐ-CP.
        </li>
      </ul>

      <h2>5. Data Retention</h2>
      <div className={styles.tableWrap}>
        <table className={styles.table}>
          <thead>
            <tr>
              <th>Data Category</th>
              <th>Retention Period</th>
              <th>Basis</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Reservation records</td>
              <td>3 years</td>
              <td>Operational records requirement</td>
            </tr>
            <tr>
              <td>Event bookings and contracts</td>
              <td>5 years</td>
              <td>Contract and tax law</td>
            </tr>
            <tr>
              <td>Payment and transaction records</td>
              <td>10 years</td>
              <td>Law on Accounting No. 88/2015/QH13</td>
            </tr>
            <tr>
              <td>Email marketing contacts</td>
              <td>Until consent is withdrawn</td>
              <td>Consent-based processing</td>
            </tr>
            <tr>
              <td>Website analytics data</td>
              <td>Up to 14 months</td>
              <td>GA4 configured retention</td>
            </tr>
            <tr>
              <td>CCTV footage</td>
              <td>90 days</td>
              <td>Security and safety purposes</td>
            </tr>
            <tr>
              <td>Enquiry and contact records</td>
              <td>2 years</td>
              <td>Purpose fulfilment</td>
            </tr>
          </tbody>
        </table>
      </div>
      <p>
        On expiry, data is securely deleted or irreversibly anonymised. You may request early deletion
        by exercising your Right to Delete (see Section 6).
      </p>

      <h2>6. Your Rights</h2>
      <p>
        Under the Law on Personal Data Protection (Law No. 91/2025/QH15), you have the following
        eleven rights:
      </p>
      <ul className={styles.rightsList}>
        {[
          ['1', 'Right to Know', 'To be informed about how your personal data is collected, used, stored, and shared.'],
          ['2', 'Right to Consent', 'To give or withhold consent before your data is processed. Consent is never assumed.'],
          ['3', 'Right to Access', 'To request a copy of all personal data we hold about you.'],
          ['4', 'Right to Withdraw Consent', 'To withdraw consent at any time, without affecting the lawfulness of prior processing.'],
          ['5', 'Right to Delete', 'To request erasure of your personal data when the original processing purpose is fulfilled or no longer necessary.'],
          ['6', 'Right to Restrict Processing', 'To request we limit how we use your data. We will respond within 72 hours.'],
          ['7', 'Right to Data Portability', 'To receive your personal data in a structured, commonly used, transferable format.'],
          ['8', 'Right to Object', 'To object to processing, including direct marketing. We will respond within 72 hours.'],
          ['9', 'Right to Complain', 'To lodge a complaint with the Ministry of Public Security (A05) or other competent Vietnamese authorities.'],
          ['10', 'Right to Claim Damages', 'To seek compensation for any unlawful processing of your personal data.'],
          ['11', 'Right to Self-Defence', 'As provided under Vietnamese civil law provisions.'],
        ].map(([num, title, desc]) => (
          <li key={num}>
            <span className={styles.rightsNum}>{num}.</span>
            <span><strong>{title}:</strong> {desc}</span>
          </li>
        ))}
      </ul>
      <div className={styles.notice}>
        <p>
          <strong>To exercise any right:</strong> Email{' '}
          <a href="mailto:info@tempohouse.com.vn">info@tempohouse.com.vn</a>. We will
          acknowledge within 3 business days and resolve within 30 days (or 72 hours for restriction
          and objection requests).
        </p>
      </div>

      <h2>7. Cookies and Tracking Technologies</h2>
      <div className={styles.tableWrap}>
        <table className={styles.table}>
          <thead>
            <tr>
              <th>Type</th>
              <th>Purpose</th>
              <th>Consent Required</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Essential cookies</td>
              <td>Website functionality</td>
              <td>No</td>
            </tr>
            <tr>
              <td>Analytics cookies (GA4)</td>
              <td>Usage pattern analysis</td>
              <td>Yes — opt-in</td>
            </tr>
            <tr>
              <td>Advertising pixels (Meta)</td>
              <td>Ad targeting and measurement</td>
              <td>Yes — opt-in</td>
            </tr>
          </tbody>
        </table>
      </div>
      <p>
        Manage cookie preferences via the consent banner displayed on your first visit. Withdraw
        consent at any time by clearing cookies or revisiting your preferences.
      </p>

      <h2>8. Data Security and Breach Notification</h2>
      <p>
        We implement appropriate technical and organisational security measures to protect your
        personal data from unauthorised access, loss, alteration, or disclosure. Our IP surveillance
        systems comply with Circular QCVN 135:2024/BTTTT.
      </p>
      <p>
        In the event of a data breach affecting your rights or legitimate interests, we will:
      </p>
      <ul>
        <li>Notify the Ministry of Public Security (A05) within <strong>72 hours</strong> of discovery;</li>
        <li>Notify affected individuals as required by Vietnamese law;</li>
        <li>Take immediate corrective action and maintain breach records.</li>
      </ul>

      <h2>9. Children&apos;s Privacy</h2>
      <p>
        Our venue and services are intended for adults aged 18 and over. We do not knowingly collect
        personal data from children under 16. If you believe we have inadvertently collected such
        data, contact us immediately at{' '}
        <a href="mailto:info@tempohouse.com.vn">info@tempohouse.com.vn</a>.
      </p>

      <h2>10. Marketing Consent — Separate From Bookings</h2>
      <p>
        In compliance with the Law on Protection of Consumers&apos; Rights No. 19/2023/QH15, consent
        to receive marketing communications is <strong>always separate</strong> from any booking or
        service contract. You can use our services fully without consenting to marketing.
      </p>

      <h2>11. Contact and Data Protection</h2>
      <div className={styles.contactBlock}>
        <p><strong>TEMPO House — Data Protection Contact</strong></p>
        <p>Email: <a href="mailto:info@tempohouse.com.vn">info@tempohouse.com.vn</a></p>
        <p>Ho Chi Minh City, Vietnam</p>
        <p style={{ marginTop: '0.75rem', fontSize: '0.875em', opacity: 0.8 }}>
          We acknowledge all requests within 3 business days and resolve within 30 days.
        </p>
      </div>

      <h2>12. Changes to This Policy</h2>
      <p>
        We may update this policy from time to time. Material changes will be communicated via our
        website. The &ldquo;Last updated&rdquo; date at the top of this document reflects the most
        recent revision. Continued use of our services following a material update constitutes
        acceptance of the revised policy.
      </p>
    </div>
  );
}

function VietnameseContent() {
  return (
    <div className={styles.content}>

      <h2>1. Về Chúng Tôi</h2>
      <p>
        TEMPO House ("chúng tôi") vận hành quán cà phê đặc sản, bar cocktail, phòng trưng bày nghệ
        thuật và không gian sự kiện tại Thành phố Hồ Chí Minh, Việt Nam, cùng trang web tại{' '}
        <a href="https://tempohouse.com.vn">tempohouse.com.vn</a>.
      </p>
      <div className={styles.contactBlock}>
        <p><strong>Bên Kiểm Soát Dữ Liệu</strong></p>
        <p>TEMPO House · Thành phố Hồ Chí Minh, Việt Nam</p>
        <p>Liên hệ bảo vệ dữ liệu: <a href="mailto:info@tempohouse.com.vn">info@tempohouse.com.vn</a></p>
      </div>
      <p>
        Chính sách này được điều chỉnh bởi <strong>Luật Bảo vệ Dữ liệu Cá nhân số 91/2025/QH15</strong>{' '}
        (có hiệu lực từ ngày 1 tháng 1 năm 2026), <strong>Nghị định số 356/2025/NĐ-CP</strong>, Luật
        An ninh mạng số 24/2018/QH14, Luật Bảo vệ Quyền lợi Người tiêu dùng số 19/2023/QH15 và
        Nghị định 91/2020/NĐ-CP về chống thư rác.
      </p>

      <h2>2. Dữ Liệu Chúng Tôi Thu Thập và Mục Đích</h2>

      <h3>2.1 Đặt Bàn</h3>
      <div className={styles.dataCard}>
        <p><strong>Dữ liệu:</strong> Họ tên, số điện thoại, email, số lượng khách, ngày/giờ, yêu cầu đặc biệt hoặc nhu cầu ăn uống.</p>
        <p><strong>Mục đích:</strong> Xác nhận và quản lý đặt chỗ; liên hệ khi có thay đổi hoặc hủy.</p>
        <p><strong>Cơ sở pháp lý:</strong> Thực hiện hợp đồng đặt bàn.</p>
        <p><strong>Lưu ý:</strong> Thông tin về chế độ ăn uống hoặc sức khỏe là <em>dữ liệu cá nhân nhạy cảm</em> và chỉ được thu thập khi có sự đồng ý rõ ràng. Bạn có thể từ chối mà không ảnh hưởng đến đặt bàn.</p>
      </div>

      <h3>2.2 Đặt Sự Kiện và Tiệc Riêng</h3>
      <div className={styles.dataCard}>
        <p><strong>Dữ liệu:</strong> Họ tên, điện thoại, email, tên công ty (nếu có), địa chỉ thanh toán, chi tiết sự kiện, số lượng khách, thông tin thanh toán.</p>
        <p><strong>Mục đích:</strong> Lên kế hoạch, xác nhận và lập hóa đơn cho sự kiện của bạn.</p>
        <p><strong>Cơ sở pháp lý:</strong> Thực hiện hợp đồng; xử lý thông tin thanh toán cần thiết cho lập hóa đơn.</p>
        <p><strong>Lưu ý:</strong> Dữ liệu tài chính/thanh toán được phân loại là <em>dữ liệu cá nhân nhạy cảm</em> theo pháp luật Việt Nam.</p>
      </div>

      <h3>2.3 Đăng Ký Email và Bản Tin</h3>
      <div className={styles.dataCard}>
        <p><strong>Dữ liệu:</strong> Họ tên, địa chỉ email.</p>
        <p><strong>Mục đích:</strong> Gửi thông tin cập nhật, thông báo sự kiện, chương trình khuyến mãi và tin tức về TEMPO House.</p>
        <p><strong>Cơ sở pháp lý:</strong> Sự đồng ý rõ ràng, riêng biệt của bạn (đăng ký tự nguyện). Đây <em>không bao giờ</em> là điều kiện để đặt bàn.</p>
        <p><strong>Hủy đăng ký:</strong> Qua liên kết trong bất kỳ email nào hoặc bằng cách gửi email đến <a href="mailto:info@tempohouse.com.vn">info@tempohouse.com.vn</a>.</p>
        <p><strong>Tuân thủ chống thư rác:</strong> Các email thương mại được gắn nhãn &ldquo;QC&rdquo; theo Nghị định 91/2020/NĐ-CP. Chúng tôi gửi không quá 3 tin nhắn thương mại mỗi ngày.</p>
      </div>

      <h3>2.4 Biểu Mẫu Liên Hệ Trực Tuyến</h3>
      <div className={styles.dataCard}>
        <p><strong>Dữ liệu:</strong> Họ tên, địa chỉ email, nội dung tin nhắn.</p>
        <p><strong>Mục đích:</strong> Phản hồi yêu cầu của bạn.</p>
        <p><strong>Cơ sở pháp lý:</strong> Sự đồng ý của bạn tại thời điểm gửi.</p>
      </div>

      <h3>2.5 Phân Tích Website (Google Analytics 4)</h3>
      <div className={styles.dataCard}>
        <p><strong>Dữ liệu:</strong> Địa chỉ IP (đã ẩn danh hóa), loại thiết bị và trình duyệt, các trang đã truy cập, thời gian phiên, nguồn giới thiệu.</p>
        <p><strong>Mục đích:</strong> Hiểu cách khách truy cập sử dụng trang web và cải thiện trải nghiệm.</p>
        <p><strong>Bên xử lý thứ ba:</strong> Google LLC, Hoa Kỳ (Google Analytics 4).</p>
        <p><strong>Chuyển dữ liệu xuyên biên giới:</strong> Dữ liệu được truyền đến máy chủ Google tại Hoa Kỳ.</p>
        <p><strong>Lưu ý:</strong> Dữ liệu hành vi trên website là <em>dữ liệu cá nhân nhạy cảm</em> theo pháp luật Việt Nam. Theo dõi phân tích <em>chỉ được kích hoạt</em> sau khi bạn đồng ý qua banner cookie.</p>
      </div>

      <h3>2.6 Meta Pixel (Quảng Cáo Facebook / Instagram)</h3>
      <div className={styles.dataCard}>
        <p><strong>Dữ liệu:</strong> Hành vi duyệt web trên trang web của chúng tôi, được liên kết với tài khoản Facebook/Instagram của bạn (nếu có).</p>
        <p><strong>Mục đích:</strong> Đo lường hiệu quả quảng cáo và hiển thị quảng cáo phù hợp.</p>
        <p><strong>Bên xử lý thứ ba:</strong> Meta Platforms, Inc., Hoa Kỳ.</p>
        <p><strong>Chuyển dữ liệu xuyên biên giới:</strong> Dữ liệu được truyền đến máy chủ Meta tại Hoa Kỳ.</p>
        <p><strong>Lưu ý:</strong> Theo dõi này <em>chỉ được kích hoạt</em> sau sự đồng ý của bạn qua banner cookie.</p>
      </div>

      <h3>2.7 Camera An Ninh (CCTV)</h3>
      <div className={styles.dataCard}>
        <p><strong>Dữ liệu:</strong> Hình ảnh video trong khuôn viên địa điểm (quán cà phê, bar, phòng trưng bày, không gian sự kiện và khu vực lối vào).</p>
        <p><strong>Mục đích:</strong> Bảo đảm an ninh, an toàn cho khách, nhân viên và tài sản.</p>
        <p><strong>Cơ sở pháp lý:</strong> Lợi ích hợp pháp trong việc duy trì cơ sở an toàn.</p>
        <p><strong>Thời gian lưu trữ:</strong> Hình ảnh được lưu giữ 90 ngày, sau đó xóa vĩnh viễn, trừ trường hợp cần cho mục đích an ninh hoặc pháp lý.</p>
        <p><strong>Quyền truy cập:</strong> Chỉ ban quản lý được ủy quyền của TEMPO House và cơ quan thực thi pháp luật khi pháp luật yêu cầu.</p>
        <p><strong>Thông báo:</strong> Biển báo CCTV được hiển thị tại tất cả các lối vào địa điểm.</p>
      </div>

      <h3>2.8 Hồ Sơ Thanh Toán</h3>
      <div className={styles.dataCard}>
        <p><strong>Dữ liệu:</strong> Số tiền giao dịch, ngày, phương thức thanh toán (loại thẻ), số tham chiếu. Chúng tôi <em>không</em> lưu trữ số thẻ thô.</p>
        <p><strong>Mục đích:</strong> Thanh toán, kế toán và nghĩa vụ lưu trữ hồ sơ pháp lý.</p>
        <p><strong>Bên xử lý:</strong> Thanh toán được xử lý bởi nhà cung cấp cổng thanh toán. Chúng tôi có thỏa thuận xử lý dữ liệu với tất cả nhà xử lý thanh toán.</p>
        <p><strong>Thời gian lưu trữ:</strong> 10 năm theo Luật Kế toán Việt Nam (Luật số 88/2015/QH13).</p>
      </div>

      <h3>2.9 Mạng Xã Hội và Liên Hệ Trực Tiếp</h3>
      <div className={styles.dataCard}>
        <p><strong>Dữ liệu:</strong> Họ tên, nội dung tin nhắn và thông tin bạn chia sẻ khi liên hệ qua Instagram, Facebook, TikTok hoặc các nền tảng khác.</p>
        <p><strong>Mục đích:</strong> Phản hồi yêu cầu của bạn.</p>
        <p><strong>Lưu ý:</strong> Các nền tảng mạng xã hội cũng thu thập dữ liệu theo chính sách bảo mật riêng của họ mà chúng tôi không kiểm soát.</p>
      </div>

      <h2>3. Chia Sẻ Dữ Liệu</h2>
      <p>Chúng tôi <strong>không bán</strong> dữ liệu cá nhân của bạn. Chúng tôi chỉ chia sẻ dữ liệu với các bên sau:</p>

      <div className={styles.tableWrap}>
        <table className={styles.table}>
          <thead>
            <tr>
              <th>Dịch Vụ</th>
              <th>Nhà Cung Cấp</th>
              <th>Quốc Gia</th>
              <th>Mục Đích</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Phân tích website</td>
              <td>Google LLC (GA4)</td>
              <td>Hoa Kỳ</td>
              <td>Phân tích lượng truy cập</td>
            </tr>
            <tr>
              <td>Pixel quảng cáo</td>
              <td>Meta Platforms, Inc.</td>
              <td>Hoa Kỳ</td>
              <td>Đo lường quảng cáo &amp; remarketing</td>
            </tr>
            <tr>
              <td>Email marketing</td>
              <td>Klaviyo, Inc.</td>
              <td>Hoa Kỳ</td>
              <td>Truyền thông tiếp thị</td>
            </tr>
            <tr>
              <td>Xử lý thanh toán</td>
              <td>Nhà cung cấp cổng thanh toán</td>
              <td>Việt Nam / khác</td>
              <td>Thu thanh toán an toàn</td>
            </tr>
          </tbody>
        </table>
      </div>

      <p>
        Chúng tôi cũng có thể tiết lộ dữ liệu cá nhân cho cơ quan thực thi pháp luật hoặc tòa án
        khi pháp luật Việt Nam yêu cầu, bao gồm Bộ Công an theo Luật An ninh mạng.
      </p>

      <h2>4. Chuyển Dữ Liệu Xuyên Biên Giới</h2>
      <p>
        Một số nhà cung cấp dịch vụ của chúng tôi đặt trụ sở ngoài Việt Nam (chủ yếu tại Hoa Kỳ).
        Khi chuyển dữ liệu cá nhân quốc tế, chúng tôi:
      </p>
      <ul>
        <li>Thông báo trước cho bạn (như trong chính sách này);</li>
        <li>Lấy sự đồng ý rõ ràng của bạn khi cần thiết;</li>
        <li>Duy trì thỏa thuận chuyển dữ liệu với tất cả các bên nhận;</li>
        <li>
          Đã nộp hoặc sẽ nộp Đánh giá Tác động Chuyển dữ liệu (TIA) lên Bộ Công an (Cục A05)
          theo Luật số 91/2025/QH15 và Nghị định 356/2025/NĐ-CP.
        </li>
      </ul>

      <h2>5. Thời Gian Lưu Trữ Dữ Liệu</h2>
      <div className={styles.tableWrap}>
        <table className={styles.table}>
          <thead>
            <tr>
              <th>Loại Dữ Liệu</th>
              <th>Thời Gian Lưu Trữ</th>
              <th>Cơ Sở</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Hồ sơ đặt bàn</td>
              <td>3 năm</td>
              <td>Yêu cầu hồ sơ vận hành</td>
            </tr>
            <tr>
              <td>Đặt sự kiện và hợp đồng</td>
              <td>5 năm</td>
              <td>Luật hợp đồng và thuế</td>
            </tr>
            <tr>
              <td>Hồ sơ thanh toán</td>
              <td>10 năm</td>
              <td>Luật Kế toán số 88/2015/QH13</td>
            </tr>
            <tr>
              <td>Danh sách email marketing</td>
              <td>Đến khi rút lại đồng ý</td>
              <td>Xử lý dựa trên đồng ý</td>
            </tr>
            <tr>
              <td>Dữ liệu phân tích website</td>
              <td>Tối đa 14 tháng</td>
              <td>Cấu hình lưu trữ GA4</td>
            </tr>
            <tr>
              <td>Hình ảnh CCTV</td>
              <td>90 ngày</td>
              <td>Mục đích an ninh và an toàn</td>
            </tr>
            <tr>
              <td>Hồ sơ liên hệ và trao đổi</td>
              <td>2 năm</td>
              <td>Hoàn thành mục đích</td>
            </tr>
          </tbody>
        </table>
      </div>
      <p>
        Khi hết thời hạn, dữ liệu sẽ được xóa an toàn hoặc ẩn danh hóa không thể phục hồi. Bạn
        có thể yêu cầu xóa sớm bằng cách thực hiện Quyền Xóa (xem Mục 6).
      </p>

      <h2>6. Quyền Của Bạn</h2>
      <p>
        Theo Luật Bảo vệ Dữ liệu Cá nhân (Luật số 91/2025/QH15), bạn có 11 quyền sau đây:
      </p>
      <ul className={styles.rightsList}>
        {[
          ['1', 'Quyền Được Biết', 'Được thông báo về cách dữ liệu cá nhân của bạn được thu thập, sử dụng, lưu trữ và chia sẻ.'],
          ['2', 'Quyền Đồng Ý', 'Đồng ý hoặc từ chối trước khi dữ liệu của bạn được xử lý. Sự đồng ý không bao giờ được giả định.'],
          ['3', 'Quyền Truy Cập', 'Yêu cầu bản sao tất cả dữ liệu cá nhân chúng tôi đang lưu giữ về bạn.'],
          ['4', 'Quyền Rút Lại Đồng Ý', 'Rút lại đồng ý bất kỳ lúc nào, không ảnh hưởng đến tính hợp pháp của việc xử lý trước đó.'],
          ['5', 'Quyền Xóa', 'Yêu cầu xóa dữ liệu cá nhân khi mục đích xử lý ban đầu đã hoàn thành hoặc không còn cần thiết.'],
          ['6', 'Quyền Hạn Chế Xử Lý', 'Yêu cầu chúng tôi giới hạn cách sử dụng dữ liệu của bạn. Chúng tôi sẽ phản hồi trong vòng 72 giờ.'],
          ['7', 'Quyền Chuyển Dữ Liệu', 'Nhận dữ liệu cá nhân của bạn ở định dạng có cấu trúc, có thể sử dụng phổ biến và có thể chuyển đổi.'],
          ['8', 'Quyền Phản Đối', 'Phản đối việc xử lý, bao gồm tiếp thị trực tiếp. Chúng tôi sẽ phản hồi trong vòng 72 giờ.'],
          ['9', 'Quyền Khiếu Nại', 'Nộp khiếu nại lên Bộ Công an (Cục A05) hoặc cơ quan có thẩm quyền Việt Nam.'],
          ['10', 'Quyền Yêu Cầu Bồi Thường', 'Yêu cầu bồi thường thiệt hại đối với việc xử lý dữ liệu cá nhân trái pháp luật.'],
          ['11', 'Quyền Tự Bảo Vệ', 'Theo quy định của pháp luật dân sự Việt Nam.'],
        ].map(([num, title, desc]) => (
          <li key={num}>
            <span className={styles.rightsNum}>{num}.</span>
            <span><strong>{title}:</strong> {desc}</span>
          </li>
        ))}
      </ul>
      <div className={styles.notice}>
        <p>
          <strong>Để thực hiện quyền:</strong> Gửi email đến{' '}
          <a href="mailto:info@tempohouse.com.vn">info@tempohouse.com.vn</a>. Chúng tôi sẽ
          xác nhận trong vòng 3 ngày làm việc và giải quyết trong vòng 30 ngày (hoặc 72 giờ đối
          với yêu cầu hạn chế và phản đối).
        </p>
      </div>

      <h2>7. Cookie và Công Nghệ Theo Dõi</h2>
      <div className={styles.tableWrap}>
        <table className={styles.table}>
          <thead>
            <tr>
              <th>Loại</th>
              <th>Mục Đích</th>
              <th>Cần Đồng Ý</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Cookie thiết yếu</td>
              <td>Chức năng trang web</td>
              <td>Không</td>
            </tr>
            <tr>
              <td>Cookie phân tích (GA4)</td>
              <td>Phân tích mẫu sử dụng</td>
              <td>Có — đăng ký tự nguyện</td>
            </tr>
            <tr>
              <td>Pixel quảng cáo (Meta)</td>
              <td>Nhắm mục tiêu và đo lường quảng cáo</td>
              <td>Có — đăng ký tự nguyện</td>
            </tr>
          </tbody>
        </table>
      </div>
      <p>
        Quản lý tùy chọn cookie qua banner đồng ý hiển thị khi truy cập lần đầu. Rút lại đồng ý
        bất kỳ lúc nào bằng cách xóa cookie hoặc xem lại tùy chọn của bạn.
      </p>

      <h2>8. Bảo Mật và Thông Báo Vi Phạm Dữ Liệu</h2>
      <p>
        Chúng tôi triển khai các biện pháp bảo mật kỹ thuật và tổ chức phù hợp để bảo vệ dữ liệu
        cá nhân của bạn khỏi truy cập, mất mát, thay đổi hoặc tiết lộ trái phép. Hệ thống camera
        an ninh của chúng tôi tuân thủ Thông tư QCVN 135:2024/BTTTT.
      </p>
      <p>Trong trường hợp vi phạm dữ liệu ảnh hưởng đến quyền lợi của bạn, chúng tôi sẽ:</p>
      <ul>
        <li>Thông báo cho Bộ Công an (Cục A05) trong vòng <strong>72 giờ</strong> kể từ khi phát hiện;</li>
        <li>Thông báo cho các cá nhân bị ảnh hưởng theo yêu cầu của pháp luật Việt Nam;</li>
        <li>Thực hiện hành động khắc phục ngay lập tức và lưu trữ hồ sơ vi phạm.</li>
      </ul>

      <h2>9. Quyền Riêng Tư Trẻ Em</h2>
      <p>
        Địa điểm và dịch vụ của chúng tôi dành cho người lớn từ 18 tuổi trở lên. Chúng tôi không
        cố ý thu thập dữ liệu cá nhân của trẻ em dưới 16 tuổi. Nếu bạn cho rằng chúng tôi đã vô
        tình thu thập dữ liệu như vậy, vui lòng liên hệ ngay với chúng tôi tại{' '}
        <a href="mailto:info@tempohouse.com.vn">info@tempohouse.com.vn</a>.
      </p>

      <h2>10. Đồng Ý Tiếp Thị — Tách Biệt Khỏi Đặt Bàn</h2>
      <p>
        Theo Luật Bảo vệ Quyền lợi Người tiêu dùng số 19/2023/QH15, sự đồng ý nhận thông tin tiếp
        thị <strong>luôn tách biệt</strong> khỏi bất kỳ đặt bàn hay hợp đồng dịch vụ nào. Bạn có
        thể sử dụng đầy đủ dịch vụ của chúng tôi mà không cần đồng ý nhận tiếp thị.
      </p>

      <h2>11. Liên Hệ Bảo Vệ Dữ Liệu</h2>
      <div className={styles.contactBlock}>
        <p><strong>TEMPO House — Liên hệ Bảo vệ Dữ liệu</strong></p>
        <p>Email: <a href="mailto:info@tempohouse.com.vn">info@tempohouse.com.vn</a></p>
        <p>Thành phố Hồ Chí Minh, Việt Nam</p>
        <p style={{ marginTop: '0.75rem', fontSize: '0.875em', opacity: 0.8 }}>
          Chúng tôi xác nhận tất cả yêu cầu trong vòng 3 ngày làm việc và giải quyết trong vòng 30 ngày.
        </p>
      </div>

      <h2>12. Thay Đổi Chính Sách</h2>
      <p>
        Chúng tôi có thể cập nhật chính sách này theo thời gian. Những thay đổi quan trọng sẽ được
        thông báo qua trang web của chúng tôi. Ngày &ldquo;Cập nhật lần cuối&rdquo; ở đầu tài liệu
        này phản ánh lần sửa đổi gần nhất.
      </p>
    </div>
  );
}
