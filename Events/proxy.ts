import { NextResponse, type NextRequest } from 'next/server'

// Auth bypassed for MVP — enable Supabase guard once canvas is validated
export function proxy(_request: NextRequest) {
  return NextResponse.next()
}

export const config = { matcher: [] }
