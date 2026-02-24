# VoiceRSS Text-to-Speech Setup Guide

## 🎯 Why VoiceRSS?
- **Completely FREE** - No credit card required!
- **Easy setup** - Just sign up and get API key
- **Good quality** - Clear Filipino and English voices
- **Reliable** - Simple API, always works
- **350 requests/day FREE** - Perfect for classroom use

## 📋 Super Simple Setup (5 minutes!)

### Step 1: Sign Up (FREE - No Credit Card!)
1. Go to: **https://www.voicerss.org/registration.aspx**
2. Fill in:
   - Name: (Your name)
   - Email: (Your email)
   - Password: (Choose a password)
   - Confirm: (Type password again)
3. Click **"Register"**
4. 🎉 Done! You now have a free account!

### Step 2: Get Your API Key
1. After registration, you'll see your **API Key** on the page
2. Or login and go to: https://www.voicerss.org/personel/
3. Copy your API key (looks like: `abc1234567890def`)

### Step 3: Add API Key to Your App
1. Open file: `C:\wamp64\www\CAPSTONE\.env`
2. Find the line: `VOICERSS_API_KEY=`
3. Paste your API key:
   ```
   VOICERSS_API_KEY=abc1234567890def
   ```
4. Save the file

### Step 4: Clear Cache
Open terminal and run:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Step 5: Test It!
1. Open: http://localhost/CAPSTONE/test-voicerss.html
2. Click **"Test Filipino Voice"**
3. 🎉 Listen to the beautiful, clear voice!

## 🗣️ Available Voices

VoiceRSS supports Filipino and English voices:
- **fil-ph** - Filipino (Female voice)
- **en-us** - English US (Female voice)

The app automatically selects the right voice based on the story's language!

## 💰 Pricing (FREE!)

**Free Tier:**
- ✅ 350 requests per day - **100% FREE**
- ✅ No credit card required
- ✅ No expiration
- ✅ MP3 audio format
- ✅ High quality (44kHz 16-bit stereo)

**Example Usage:**
- Average story: 1 request
- 30 students × 10 stories each = 300 requests/day
- Still within FREE limit! ✅

**If you exceed 350/day:**
- Premium: $49/year for 10,000 requests/day
- But for classroom use, free tier is more than enough!

## 🔒 Security

Your API key is safe:
- ✅ Stored in `.env` file (not in code)
- ✅ `.env` is in `.gitignore` (won't be uploaded to GitHub)
- ✅ Laravel backend handles all API calls (API key never exposed to users)

## 🎨 Voice Quality Settings

Current settings (optimized for clarity):
- **Format:** MP3
- **Quality:** 44kHz 16-bit stereo (high quality)
- **Speed:** -2 (slightly slower for clearer pronunciation)

Want to adjust? Edit: `app/Http/Controllers/TextToSpeechController.php`

Line 34: Change speed
```php
'r' => '-2',  // Speed: -10 (slowest) to 10 (fastest)
```

## 🛠️ Troubleshooting

### Error: "API key not configured"
**Solution:** 
1. Make sure you added the API key to `.env`
2. Run: `php artisan config:clear`
3. Check for typos in the API key

### Error: "Invalid API key"
**Solution:**
1. Go to: https://www.voicerss.org/personel/
2. Copy your API key again
3. Make sure you copied it completely (including all characters)
4. Paste it in `.env` without spaces

### Error: "Daily limit reached"
**Solution:** You've used 350 requests today
- Wait until midnight (resets daily)
- Or upgrade to premium ($49/year)
- Most schools will never hit this limit

### Audio sounds robotic
**Solution:** This is normal for free TTS
- VoiceRSS voices are good but not as natural as Google Wavenet
- Still much better than browser's built-in TTS!
- Filipino voice is clear and understandable

### No sound / Audio doesn't play
**Solution:**
1. Check internet connection (API needs internet)
2. Check browser console (F12) for errors
3. Try different browser (Chrome recommended)
4. Make sure volume is turned on

## 📊 API Limits

| Tier | Requests/Day | Cost | Credit Card? |
|------|--------------|------|--------------|
| Free | 350 | $0 | ❌ No |
| Premium | 10,000 | $49/year | ✅ Yes |
| Enterprise | Unlimited | Contact | ✅ Yes |

**Recommendation:** Start with FREE tier. You probably won't need to upgrade!

## 🎓 Perfect for Schools

Why VoiceRSS is great for education:
- ✅ No payment setup required
- ✅ Works immediately after signup
- ✅ 350 requests = plenty for daily classroom use
- ✅ Simple API = reliable and fast
- ✅ Supports Filipino language
- ✅ Good pronunciation for learning

## 📞 Support

**VoiceRSS Support:**
- Website: https://www.voicerss.org/
- Documentation: https://www.voicerss.org/api/
- Email: info@voicerss.org

**App Issues:**
1. Check browser console (F12) for errors
2. Check Laravel logs: `storage/logs/laravel.log`
3. Verify API key in `.env` is correct
4. Make sure internet connection is working

## ⚙️ Technical Details

**API Endpoint:**
```
https://api.voicerss.org/
```

**Request Parameters:**
- `key` - Your API key
- `src` - Text to convert
- `hl` - Language (fil-ph or en-us)
- `c` - Codec (MP3)
- `f` - Format (44khz_16bit_stereo)
- `r` - Rate/Speed (-10 to 10)

**Response:**
- Direct MP3 audio file (not JSON)
- Base64 encoded in our app
- Played through browser's Audio API

## ✅ What's Implemented

- ✅ VoiceRSS API integration
- ✅ Filipino voice support (fil-ph)
- ✅ English voice support (en-us)
- ✅ High quality audio (44kHz stereo)
- ✅ Automatic language detection
- ✅ Loading animations
- ✅ Error handling
- ✅ Secure API key storage
- ✅ Backend proxy (API key hidden)
- ✅ Daily limit handling
- ✅ Professional UI

## 🚀 Next Steps

1. **Register** at VoiceRSS.org (2 minutes)
2. **Copy API key** from your account page
3. **Add to .env** file
4. **Clear cache** with artisan commands
5. **Test** at test-voicerss.html
6. **Enjoy** high-quality Filipino text-to-speech! 🎉

**No credit card. No payment. Just free, quality text-to-speech!** ✨
